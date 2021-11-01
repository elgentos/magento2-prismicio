<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\ResourceModel\Route;

use Elgentos\PrismicIO\Model\Route;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Zend_Db_Expr;

class Collection extends AbstractCollection
{
    /** @var StoreFactory */
    private StoreFactory $storeFactory;

    /**
     * Constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param StoreFactory           $storeFactory
     * @param AdapterInterface|null  $connection
     * @param AbstractDb|null        $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreFactory $storeFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeFactory = $storeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Elgentos\PrismicIO\Model\Route',
            'Elgentos\PrismicIO\Model\ResourceModel\Route'
        );
    }

    /**
     * Filter the results by request path
     *
     * @param string $requestPath
     *
     * @return void
     */
    public function filterByRequestPath(string $requestPath): void
    {
        $this->getSelect()
            ->where("? like concat(route, '%')", $requestPath);
    }

    /**
     * Filter the results by store ID
     *
     * @param int $storeId
     *
     * @return void
     */
    public function filterByStoreId(int $storeId): void
    {
        /** @var Store $storeResource */
        $storeResource = $this->storeFactory->create();
        $select        = $this->getSelect();

        $select->join(
            ['stores' => $storeResource->getMainTable()],
            'main_table.route_id = stores.route_id',
            []
        );
        $select->columns(
            new Zend_Db_Expr(
                'group_concat(distinct stores.store_id separator ",") as ' . Route::STORE_IDS
            )
        );

        $select->where('stores.store_id in(?)', [0, $storeId]);
    }

    /**
     * Filter the results by status
     *
     * @param bool $enabled
     *
     * @return void
     */
    public function filterByStatus(bool $enabled = true): void
    {
        $this->addFieldToFilter('status', $enabled ? '1' : '0');
    }
}
