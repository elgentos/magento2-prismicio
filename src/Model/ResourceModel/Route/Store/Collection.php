<?php

namespace Elgentos\PrismicIO\Model\ResourceModel\Route\Store;

use Elgentos\PrismicIO\Model\Route;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Elgentos\PrismicIO\Model\Route\Store::class, \Elgentos\PrismicIO\Model\ResourceModel\Route\Store::class);
    }


    public function addRouteFilter(Route $route): void
    {
        $this->addFilter('route_id', $route->getId());
    }
}
