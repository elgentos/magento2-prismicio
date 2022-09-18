<?php
namespace Elgentos\PrismicIO\Model\ResourceModel;

class Route extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('prismicio_route', 'route_id');
    }
}
