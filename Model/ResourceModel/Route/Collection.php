<?php

namespace Elgentos\PrismicIO\Model\ResourceModel\Route;

use Elgentos\PrismicIO\Model\Route;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /** @var StoreFactory */
    private $storeFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StoreFactory $storeFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeFactory = $storeFactory;
    }


    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\Route', 'Elgentos\PrismicIO\Model\ResourceModel\Route');
    }

    public function filterByRequestPath(string $requestPath): void
    {
        $this->getSelect()
                ->where("? like concat(route, '%')", $requestPath);
    }

    public function filterByStoreId(int $storeId): void
    {
        /** @var Store $storeResource */
        $storeResource = $this->storeFactory->create();

        $select = $this->getSelect();

        $select->join(['stores' => $storeResource->getMainTable()], 'main_table.route_id = stores.route_id', []);
        $select->columns(new \Zend_Db_Expr('group_concat(distinct stores.store_id separator ",") as ' . Route::STORE_IDS));

        $select->where('stores.store_id in(?)', [0, $storeId]);
    }

    public function filterByStatus(bool $enabled = true): void
    {
        $this->addFieldToFilter('status', $enabled ? '1' : '0');
    }
}
