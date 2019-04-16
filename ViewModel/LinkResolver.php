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
    )
    {
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
        switch ($link->link_type) {
            case 'Document':

                return $this->getDocumentUrl($link);

            case 'Media':
                return $this->getMediaUrl($link);

            default:
                return $this->urlBuilder->getBaseUrl();
        }

    }

    public function getMediaUrl(\stdClass $link): ?string
    {
        return $link->url ?? null;
    }

    public function getDocumentUrl(\stdClass $link): ?string
    {
        $uid = $link->uid ?? null;
        $contentType = $link->type ?? null;

        if (! $uid || !$contentType) {
            return null;
        }


        try {
            $store = $this->storeManager->getStore();
            $route = $this->routeRepository->getByContentType((string)$contentType, +$store->getId());

            $url = trim($route->getRoute(), '/') . '/' . $uid;

            return $this->urlBuilder->getUrl('', [
                '_scope' => $store,
                '_rewrite' => false,
                '_direct' => $url
            ]);
        } catch (RouteNotFoundException $e) {
            return $this->urlBuilder->getBaseUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

}