<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Route;

use Elgentos\PrismicIO\Api\Data\Route\StoreInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Store extends AbstractModel implements StoreInterface, IdentityInterface
{
    private const CACHE_TAG = 'elgentos_prismicio_route_store';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route\Store');
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getRouteId(): int
    {
        return (int) $this->_getData('route_id');
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }
}
