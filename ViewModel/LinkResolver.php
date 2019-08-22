<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 11:45
 */

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Api\RouteRepositoryInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\LinkResolver as LinkResolverAbstract;

class LinkResolver extends LinkResolverAbstract
{

    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    public function __construct(
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        RouteRepositoryInterface $routeRepository
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->routeRepository = $routeRepository;
    }

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
        $linkType = $link->link_type ?? 'Document';

        return $linkType === 'Media' ?
            $this->getMediaUrl($link) :
            $this->resolveRouteUrl($link);
    }

    public function getMediaUrl(\stdClass $link): ?string
    {
        return $link->url ?? null;
    }

    public function getStore(\stdClass $link): StoreInterface
    {
        $storeId = $link->store ?? $link->store_id ?? null;
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

            $url = trim($route->getRoute(), '/') . '/' . $uid;
            return trim($this->urlBuilder->getUrl($url, [
                '_scope' => $store,
                '_use_rewrite' => true,
                '_nosid' => true
            ]), '/');

        } catch (RouteNotFoundException $e) {
            // Return direct page
            return $this->resolveDirectPage($link);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function resolveDirectPage(\stdClass $link): ?string
    {
        $store = $this->getStore($link);

        $id = $link->id ?? null;
        $uid = $link->uid ?? null;
        $contentType = $link->type ?? null;

        $routeParams = [
            '_scope' => $store,
            '_use_rewrite' => true,
            '_nosid' => true
        ];

        // Assign parameters
        if ($uid) {
            $contentType && ($routeParams['type'] = $contentType);
            $routeParams['uid'] = $uid;
        } elseif (!$id) {
            // No id to match on
            return '';
        } else {
            $routeParams['id'] = $id;
        }

        return $this->urlBuilder->getUrl('prismicio/direct/page', $routeParams);
    }
}
