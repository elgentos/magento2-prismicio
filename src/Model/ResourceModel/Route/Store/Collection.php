<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\ResourceModel\Route\Store;

use Elgentos\PrismicIO\Model\Route;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Elgentos\PrismicIO\Model\Route\Store',
            'Elgentos\PrismicIO\Model\ResourceModel\Route\Store'
        );
    }

    /**
     * Add the route to the filters
     *
     * @param Route $route
     *
     * @return void
     */
    public function addRouteFilter(Route $route): void
    {
        $this->addFilter('route_id', $route->getId());
    }
}
