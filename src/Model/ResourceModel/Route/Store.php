<?php

namespace Elgentos\PrismicIO\Model\ResourceModel\Route;

class Store extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('prismicio_route_store', 'route_store_id');
    }
}
