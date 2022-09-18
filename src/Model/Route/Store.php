<?php
namespace Elgentos\PrismicIO\Model\Route;

class Store extends \Magento\Framework\Model\AbstractModel implements \Elgentos\PrismicIO\Api\Data\Route\StoreInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'prismicio_route_store';

    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route\Store');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getRouteId(): int
    {
        return +$this->_getData('route_id');
    }

    public function getStoreId(): int
    {
        return +$this->_getData('store_id');
    }
}
