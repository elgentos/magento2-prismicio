<?php

namespace Elgentos\PrismicIO\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Store\Api\Data\StoreInterface;
use stdClass;

class GetStoreView
{
    public function __construct(private readonly StoreManagerInterface $storeManager, private readonly ConfigurationInterface $configuration)
    {
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