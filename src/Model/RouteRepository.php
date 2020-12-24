<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Api\RouteRepositoryInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Collection;
use Elgentos\PrismicIO\Model\ResourceModel\Route\CollectionFactory;

class RouteRepository implements RouteRepositoryInterface
{
    /**@var CollectionFactory */
    private $collectionFactory;

    /** @var array */
    protected $routes = [];

    /** @var array */
    protected $routesById = [];

    /** @var array */
    protected $routesByContentType = [];

    /** @var bool */
    protected $initialized = false;

    /**
     * Constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $id
     *
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function get(int $id): RouteInterface
    {
        $this->initialize();
        if (!isset($this->routesById[$id])) {
            throw new RouteNotFoundException(
                sprintf('Route #%s not found', $id)
            );
        }

        return $this->routesById[$id];
    }

    /**
     * Get Route by content type
     *
     * @param string $contentType
     * @param int    $storeId
     *
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function getByContentType(string $contentType, int $storeId): RouteInterface
    {
        $this->initialize();

        if (!isset($this->routesByContentType[$contentType])) {
            throw new RouteNotFoundException(
                sprintf(
                    'Route not found for content type "%s"',
                    $contentType
                )
            );
        }

        if (!isset($this->routesByContentType[$contentType][$storeId])) {
            throw new RouteNotFoundException(
                sprintf(
                    'Route not found for store #%s in content type "%s"',
                    $storeId,
                    $contentType
                )
            );
        }

        return $this->routesByContentType[$contentType][$storeId];
    }

    /**
     * Initialize the route collection
     *
     * @return void
     */
    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        /** @var Collection $collection */
        $collection   = $this->collectionFactory->create();
        $this->routes = $collection->getItems();

        /** @var RouteInterface $route */
        foreach ($this->routes as $route) {
            $this->routesById[+$route->getId()] = $route;

            foreach ($route->getStoreIds() as $storeId) {
                if (!isset($this->routesByContentType[$route->getContentType()])) {
                    $this->routesByContentType[$route->getContentType()] = [];
                }

                $this->routesByContentType[$route->getContentType()][(int)$storeId] = $route;
            }
        }
    }
}
