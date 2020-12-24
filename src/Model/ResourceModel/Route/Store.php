<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\ResourceModel\Route;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Store extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('prismicio_route_store', 'route_store_id');
    }
}