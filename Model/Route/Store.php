<?php
namespace Elgentos\PrismicIO\Model\Route;

class Store extends \Magento\Framework\Model\AbstractModel implements \Elgentos\PrismicIO\Api\Data\Route\StoreInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'elgentos_prismicio_route_store';

    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route\Store');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}
