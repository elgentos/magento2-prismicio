<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 11:45
 */

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Api\RouteRepositoryInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Prismic\LinkResolver as LinkResolverAbstract;

class LinkResolver extends LinkResolverAbstract implements ArgumentInterface
{
    private array $urlCache = [];
    private array $cachedLanguageStoreIds;

    /**
     * LinkResolver constructor.
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param RouteRepositoryInterface $routeRepository
     * @param UrlFinderInterface $urlFinder
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly UrlFinderInterface $urlFinder,
        private readonly ConfigurationInterface $configuration
    ) {}

    /**
     * Returns the application-specific URL related to this document link
     *
     *
     * @param object $link The document link
     *
     * @return string|null The URL of the link
     */
    public function resolve($link): ?string
    {
        return $this->resolveRouteUrl($link);
    }

    public function getStore(\stdClass $link): StoreInterface
    {
        $storeId = $link->store ?? $link->store_id ?? $this->getStoreIdFromLink($link) ?? null;
        return $this->storeManager->getStore($storeId);
    }

    public function resolveRouteUrl(\stdClass $link): ?string
    {
        $uid = $link->uid ?? null;
        $contentType = $link->type ?? null;

        if (! $uid || !$contentType) {
            return $this->resolveDirectPage($link);
        }

        try {
            $store = $this->getStore($link);
            $route = $this->routeRepository->getByContentType((string)$contentType, +$store->getId());

            $data = ['_direct' => trim($route->getRoute(), '/') . '/' . $uid];
            return $this->getCachedUrl($store, $data, ['_ROUTED', $route->getId()], '');
        } catch (RouteNotFoundException) {
            // Return direct page
            return $this->resolveDirectPage($link);
        } catch (\Exception) {
            return null;
        }
    }

    public function resolveDirectPage(\stdClass $link): ?string
    {
        $store = $this->getStore($link);

        $id = $link->id ?? null;
        $uid = $link->uid ?? null;
        $contentType = $link->type ?? null;

        // Unable to resolve
        if (!$uid && !$id) {
            return '';
        }

        $data = [];
        $contentType && ($data['type'] = $contentType);

        if ($uid) {
            $data['uid'] = $uid;
        } else {
            $data['id'] = $uid;
        }

        return $this->getCachedUrl($store, $data, ['_DIRECT_']);
    }

    public function getCachedUrl(
        StoreInterface $store,
        array $data,
        array $cacheKeys = [],
        string $routePath = 'prismicio/direct/page'
    ): string
    {
        $cacheKeys = [
            $store->getId(),
            ...$cacheKeys,
            ...$data,
            $routePath
        ];

        $cacheKey = implode('|', $cacheKeys);
        return $this->urlCache[$cacheKey] ?? ($this->urlCache[$cacheKey] = $this->getUrl($store, $data, $routePath));
    }

    public function getUrl(StoreInterface $store, array $data, string $routePath = 'prismicio/direct/page'): string
    {
        $routeParams = [
            '_nosid' => true,
            '_scope' => $store
        ];
        $targetPath = $routePath . '/' . $this->createParams($data);

        if ($store->getConfig('web/default/front') === $targetPath) {
            // Homepage
            return $store->getBaseUrl();
        }

        if ($routePath && ($rewriteUrl = $this->getUrlRewrite($targetPath, $store))) {
            // Magento's url resolver is so stupid Framework/Url.php:748
            // Talking about single responsibility I would love to give this to Magento's URL resolver
            // I just cant because they forgot to forward a few parameters
            return $this->getFormattedUrl($store->getBaseUrl() . $rewriteUrl->getRequestPath());
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

    /**
     * Generate route params
     *
     * @param array $data
     * @return string
     */
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
        return $this->urlFinder->findOneByData([
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => 0,
            UrlRewrite::STORE_ID => $store->getId()
        ]);
    }

    /**
     * Resolve store id from $link->lang to a valid storeId
     *
     * @param \stdClass $link
     * @return int|null
     */
    public function getStoreIdFromLink(\stdClass $link): ?int
    {
        if (! isset($link->lang)) {
            return null;
        }

        return $this->getLanguageStoreIds()[$link->lang] ?? null;
    }

    /**
     * Get reversed language code to store id mapping
     *
     * @return array
     */
    private function getLanguageStoreIds(): array
    {
        if (isset($this->cachedLanguageStoreIds)) {
            return $this->cachedLanguageStoreIds;
        }

        $languageStoreIds = [];
        foreach ($this->storeManager->getStores() as $store) {
            $languageCode = $this->configuration->getContentLanguage($store);
            $languageStoreIds[$languageCode] ??= +$store->getId();
        }

        /**
         * If locale fallback is enabled, make sure those requests will be directed to this store
         */
        $currentStore = $this->storeManager->getStore();
        if ($this->configuration->hasContentLanguageFallback($currentStore)) {
            $languageStoreIds[$this->configuration->getContentLanguageFallback($currentStore)] = +$currentStore->getId();
        }

        $this->cachedLanguageStoreIds = $languageStoreIds;
        return $languageStoreIds;
    }

}
