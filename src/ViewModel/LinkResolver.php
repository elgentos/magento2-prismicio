<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Api\RouteRepositoryInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;
use Exception;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Prismic\LinkResolver as LinkResolverAbstract;
use stdClass;

class LinkResolver extends LinkResolverAbstract implements ArgumentInterface
{
    private UrlInterface $urlBuilder;

    private StoreManagerInterface $storeManager;

    private RouteRepositoryInterface $routeRepository;

    private UrlFinderInterface $urlFinder;

    private array $urlCache = [];

    private ConfigurationInterface $configuration;

    private ?array $cachedLanguageStoreIds;

    public function __construct(
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        RouteRepositoryInterface $routeRepository,
        UrlFinderInterface $urlFinder,
        ConfigurationInterface $configuration
    ) {
        $this->urlBuilder      = $urlBuilder;
        $this->storeManager    = $storeManager;
        $this->routeRepository = $routeRepository;
        $this->urlFinder       = $urlFinder;
        $this->configuration   = $configuration;
    }

    /**
     * Returns the application-specific URL related to this document link
     */
    public function resolve($link): ?string
    {
        /** @var string $linkType */
        $linkType = $link->link_type ?? 'Document';

        return $linkType === 'Media' ?
            $this->getMediaUrl($link) :
            $this->resolveRouteUrl($link);
    }

    public function getMediaUrl(stdClass $link): ?string
    {
        return $link->url ?? null;
    }

    public function getStore(stdClass $link): StoreInterface
    {
        $storeId = $link->store
            ?? $link->store_id
            ?? $this->getStoreIdFromLink($link)
            ?? null;

        return $this->storeManager->getStore($storeId);
    }

    public function resolveRouteUrl(stdClass $link): ?string
    {
        $uid         = $link->uid ?? null;
        $contentType = $link->type ?? null;

        if (!$uid || !$contentType) {
            return $this->resolveDirectPage($link);
        }

        try {
            $store = $this->getStore($link);
            $route = $this->routeRepository->getByContentType(
                (string) $contentType,
                (int) $store->getId()
            );

            $cacheKey = implode(
                '|',
                [
                    '_ROUTED_',
                    $route->getId(),
                    $store->getId(),
                    $uid
                ]
            );

            if (isset($this->urlCache[$cacheKey])) {
                return $this->urlCache[$cacheKey];
            }

            $data = [
                '_direct' => trim($route->getRoute(), '/') . '/' . $uid
            ];

            return $this->urlCache[$cacheKey] = $this
                ->getUrl($store, $data, '');
        } catch (RouteNotFoundException $e) {
            // Return direct page
            return $this->resolveDirectPage($link);
        } catch (Exception $e) {
            return null;
        }
    }

    public function resolveDirectPage(stdClass $link): ?string
    {
        $store       = $this->getStore($link);
        $id          = $link->id ?? null;
        $uid         = $link->uid ?? null;
        $contentType = $link->type ?? null;

        // Unable to resolve
        if (!$uid && !$id) {
            return '';
        }

        $data = [];

        if ($uid) {
            $data['uid'] = $uid;
        } else {
            $data['id'] = $uid;
        }

        $cacheKey = implode(
            '|',
            [
            '_DIRECT_',
            $store->getId(),
            $contentType,
            $uid,
            $id
            ]
        );

        if (isset($this->urlCache[$cacheKey])) {
            return $this->urlCache[$cacheKey];
        }

        return $this->urlCache[$cacheKey] = $this->getUrl($store, $data);
    }

    public function getUrl(
        Store $store,
        array $data,
        string $routePath = 'prismicio/direct/page'
    ): string {
        $routeParams = [
            '_nosid' => true,
            '_scope' => $store
        ];
        $targetPath  = $routePath . '/' . $this->createParams($data);

        if ($store->getConfig('web/default/front') === $targetPath) {
            // Homepage
            return $store->getBaseUrl();
        }

        $rewriteUrl = $this->getUrlRewrite($targetPath, $store);

        if ($routePath && $rewriteUrl) {
            // Magento's url resolver is so stupid Framework/Url.php:748
            // Talking about single responsibility I would love to give this to Magento's URL resolver
            // I just cant because they forgot to forward a few parameters
            return $this->getFormattedUrl(
                $store->getBaseUrl() . $rewriteUrl->getRequestPath()
            );
        }

        if (isset($data['_direct'])) {
            // See above statement
            return $this->getFormattedUrl($store->getBaseUrl() . $data['_direct']);
        }

        // Set route params and merge with requested parameters
        $routeParams = array_merge($routeParams, $data);

        return $this->getFormattedUrl($this->urlBuilder->getUrl($routePath, $routeParams));
    }

    public function getFormattedUrl(string $url): string
    {
        return rtrim($url, '/');
    }

    private function createParams(array $data): string
    {
        $params = [];

        foreach ($data as $key => $value) {
            $params[] = $key . '/' . $value;
        }

        return implode('/', $params);
    }

    public function getUrlRewrite(string $targetPath, StoreInterface $store): ?UrlRewrite
    {
        return $this->urlFinder->findOneByData(
            [
                UrlRewrite::TARGET_PATH => $targetPath,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::STORE_ID => $store->getId()
            ]
        );
    }

    public function getStoreIdFromLink(stdClass $link): ?int
    {
        if (! isset($link->lang)) {
            return null;
        }

        return $this->getLanguageStoreIds()[$link->lang] ?? null;
    }

    private function getLanguageStoreIds(): array
    {
        if (null !== $this->cachedLanguageStoreIds) {
            return $this->cachedLanguageStoreIds;
        }

        $languageStoreIds = [];

        foreach ($this->storeManager->getStores() as $store) {
            $languageCode                    = $this->configuration->getContentLanguage($store);
            $languageStoreIds[$languageCode] = $languageStoreIds[$languageCode]
                ?? (int)$store->getId();
        }

        /**
         * If locale fallback is enabled, make sure those requests will be directed to this store
         */
        $currentStore = $this->storeManager->getStore();
        if ($this->configuration->hasContentLanguageFallback($this->storeManager->getStore())) {
            $languageStoreIds[$this->configuration->getContentLanguageFallback($currentStore)]
                = (int) $currentStore->getId();
        }

        /**
         * If locale fallback is enabled, make sure those requests will be directed to this store
         */
        $currentStore = $this->storeManager->getStore();
        if ($this->configuration->hasContentLanguageFallback($this->storeManager->getStore())) {
            $languageStoreIds[$this->configuration->getContentLanguageFallback($currentStore)]
                = (int) $currentStore->getId();
        }

        $this->cachedLanguageStoreIds = $languageStoreIds;

        return $languageStoreIds;
    }
}
