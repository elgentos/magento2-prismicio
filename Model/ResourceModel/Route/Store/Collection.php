<?php

namespace Elgentos\PrismicIO\Model\ResourceModel\Route\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\Route\Store', 'Elgentos\PrismicIO\Model\ResourceModel\Route\Store');
    }

}
