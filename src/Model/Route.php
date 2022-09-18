<?php

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\Data\Route\StoreInterface;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\Collection;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Route extends \Magento\Framework\Model\AbstractModel implements \Elgentos\PrismicIO\Api\Data\RouteInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'prismicio_route';
    const STORE_IDS = '_store_ids';

    /** @var CollectionFactory */
    private $storeCollectionFactory;

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

    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Generate uid for requested path
     *
     * @param string $requestPath
     * @return string
     */
    public function getUidForRequestPath(string $requestPath): string
    {
        return trim(preg_replace('#^' . preg_quote($this->_getData('route'), '#') . '#', '', $requestPath), '/');
    }

    public function getId(): ?int
    {
        $id = +parent::getId();
        return $id > 0 ? $id : null;
    }

    public function getTitle(): string
    {
        return (string)$this->_getData('title');
    }

    public function setTitle(string $title): void
    {
        $this->setData('title', $title);
    }

    public function getContentType(): string
    {
        return (string)$this->_getData('content_type');
    }

    public function setContentType(string $contentType): void
    {
        $this->setData('content_type', $contentType);
    }

    public function getRoute(): string
    {
        return (string)$this->_getData('route');
    }

    public function setRoute(string $route): void
    {
        $this->setData('route', $route);
    }

    public function getStatus(): bool
    {
        return !!$this->_getData('status');
    }

    public function setStatus(bool $status): void
    {
        $this->setData('route', $status ? '1' : '0');
    }

    public function getCreatedAt(): string
    {
        return (string)$this->_getData('created_at');
    }

    public function getUpdatedAt(): string
    {
        return (string)$this->_getData('updated_at');
    }

    public function getStoreIds(): array
    {
        $storeIds = null;
        if (! $this->hasData(self::STORE_IDS) && $this->getId()) {
            /** @var Collection $collection */
            $collection = $this->storeCollectionFactory->create();

            $collection->addRouteFilter($this);

            $storeIds = array_map(function (StoreInterface $store) {
                return $store->getStoreId();
            }, $collection->getItems());

            $this->setData(self::STORE_IDS, $storeIds);
        }

        $storeIds = $storeIds ?? $this->_getData(self::STORE_IDS) ?? [];
        if (! is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        return $storeIds;
    }
}
