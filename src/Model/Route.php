<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\Data\Route\StoreInterface;
use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\Collection;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Route extends AbstractModel implements RouteInterface, IdentityInterface
{
    public const CACHE_TAG = 'elgentos_prismicio_route',
        STORE_IDS          = '_store_ids';

    /** @var CollectionFactory */
    private $storeCollectionFactory;

    /**
     * Constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param CollectionFactory     $storeCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $storeCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeCollectionFactory = $storeCollectionFactory;
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route');
    }

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Generate uid for requested path
     *
     * @param string $requestPath
     *
     * @return string
     */
    public function getUidForRequestPath(string $requestPath): string
    {
        return trim(
            preg_replace(
                '#^' . preg_quote($this->_getData('route'), '#') . '#',
                '',
                $requestPath
            ),
            '/'
        );
    }

    /**
     * @inheritDoc
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        $id = (int) parent::getId();

        return $id > 0 ? $id : null;
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    /**
     * @inheritDoc
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->setData('title', $title);
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getContentType(): string
    {
        return (string)$this->_getData('content_type');
    }

    /**
     * @inheritDoc
     *
     * @param string $contentType
     *
     * @return void
     */
    public function setContentType(string $contentType): void
    {
        $this->setData('content_type', $contentType);
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getRoute(): string
    {
        return (string)$this->_getData('route');
    }

    /**
     * @inheritDoc
     *
     * @param string $route
     *
     * @return void
     */
    public function setRoute(string $route): void
    {
        $this->setData('route', $route);
    }

    /**
     * @inheritDoc
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getStatus(): bool
    {
        return !!$this->_getData('status');
    }

    /**
     * @inheritDoc
     *
     * @param bool $status
     *
     * @return void
     */
    public function setStatus(bool $status): void
    {
        $this->setData('route', $status ? '1' : '0');
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_getData('created_at');
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->_getData('updated_at');
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function getStoreIds(): array
    {
        $storeIds = null;

        if (!$this->hasData(self::STORE_IDS) && $this->getId()) {
            /** @var Collection $collection */
            $collection = $this->storeCollectionFactory->create();

            $collection->addRouteFilter($this);

            $storeIds = array_map(
                function (StoreInterface $store) {
                    return $store->getStoreId();
                },
                $collection->getItems()
            );

            $this->setData(self::STORE_IDS, $storeIds);
        }

        $storeIds = $storeIds ?? $this->_getData(self::STORE_IDS) ?? [];

        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        return $storeIds;
    }
}
