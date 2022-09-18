<?php

namespace Elgentos\PrismicIO\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Store\Api\Data\StoreInterface;
use stdClass;

class GetStoreView
{
    private StoreManagerInterface $storeManager;

    private ConfigurationInterface $configuration;

    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigurationInterface $configuration
    ) {
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
    }

    public function getCurrentStoreView(stdClass $document): ?StoreInterface
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            if ($this->configuration->getContentLanguage($store) === $document->lang) {
                return $store;
            }
        }

        return null;
    }
}