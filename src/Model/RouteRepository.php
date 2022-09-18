<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 14:05
 */

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Api\RouteRepositoryInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Collection;
use Elgentos\PrismicIO\Model\ResourceModel\Route\CollectionFactory;

class RouteRepository implements RouteRepositoryInterface
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /** @var []RouteInterface */
    protected $routes = [];

    /** @var []RouteInterface */
    protected $routesById = [];
    /** @var [][]RouteInterface */
    protected $routesByContentType = [];
    /** @var bool */
    protected $initialized = false;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function get(int $id): RouteInterface
    {
        $this->initialize();
        if (! isset($this->routesById[$id])) {
            throw new RouteNotFoundException(sprintf('Route #%s not found', $id));
        }

        return $this->routesById[$id];
    }

    /**
     * Get Route by content type
     *
     * @param string $contentType
     * @param int $storeId
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function getByContentType(string $contentType, int $storeId): RouteInterface
    {
        $this->initialize();
        if (! isset($this->routesByContentType[$contentType])) {
            throw new RouteNotFoundException(sprintf('Route not found for content type "%s"', $contentType));
        }

        if (! isset($this->routesByContentType[$contentType][$storeId])) {
            throw new RouteNotFoundException(sprintf('Route not found for store #%s in content type "%s"', $storeId, $contentType));
        }

        return $this->routesByContentType[$contentType][$storeId];
    }

    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->routes = $collection->getItems();

        /** @var RouteInterface $route */
        foreach ($this->routes as $route) {
            $this->routesById[+$route->getId()] = $route;

            foreach ($route->getStoreIds() as $storeId) {
                if (! isset($this->routesByContentType[$route->getContentType()])) {
                    $this->routesByContentType[$route->getContentType()] = [];
                }

                $this->routesByContentType[$route->getContentType()][+$storeId] = $route;
            }
        }
    }
}
