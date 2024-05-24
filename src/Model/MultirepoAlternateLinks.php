<?php

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api\CacheProxy;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Api as PrismicApi;
use Prismic\Exception\RequestFailureException;
use Prismic\Predicates;

class MultirepoAlternateLinks
{

    private array $documentLanguageCache = [];

    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly StoreManagerInterface $storeManager,
        private readonly CacheProxy $cacheProxy,
    ) {}

    public function getAlternateLinks(
        array $mappedDocumentsToLanguage,
        \stdClass $document
    ): array {
        $configuration = $this->configuration;;
        $storeManager = $this->storeManager;

        if (! $configuration->getMultiRepoEnabled($storeManager->getStore())) {
            return $mappedDocumentsToLanguage;
        }

        foreach ($storeManager->getStores() as $store) {
            if (! $configuration->getApiEnabled($store)) {
                continue;
            }

            if (! $configuration->getMultiRepoEnabled($store)) {
                continue;
            }

            $multiRepoField = $configuration->getMultiRepoField($store);
            $referenceField = $document->data->{$multiRepoField} ?? null;
            if (! $referenceField) {
                continue;
            }

            $magentoLanguage = strtolower(str_replace('_', '-', $store->getConfig('general/locale/code')));
            $documentType = $document->type ?? $configuration->getContentType($store);

            if (isset($mappedDocumentsToLanguage[$magentoLanguage])) {
                // Already found in the current document
                continue;
            }

            // Cache for subsequent requests
            $cacheKey = $magentoLanguage . '-' . $documentType . '-' . $referenceField;

            if (isset($this->documentLanguageCache[$cacheKey])) {
                false !== $this->documentLanguageCache[$cacheKey] && ($mappedDocumentsToLanguage[$magentoLanguage] = $this->documentLanguageCache[$cacheKey]);
                continue;
            }

            // Fetch document from API
            $api = $this->create($store);

            try {
                $alternateDocument = $api->queryFirst(
                    Predicates::at('my.' . $documentType . '.' . $multiRepoField, $referenceField),
                    [
                        'lang' => $configuration->getContentLanguage($store)
                    ]
                );
            } catch (RequestFailureException $e) {
                $this->documentLanguageCache[$cacheKey] = false;
                continue;
            }

            $this->documentLanguageCache[$cacheKey] =
                $mappedDocumentsToLanguage[$magentoLanguage] =
                    $alternateDocument;
        }

        return $mappedDocumentsToLanguage;

    }

    private function create(StoreInterface $store)
    {
        $configuration = $this->configuration;

        $apiEndpoint = $configuration->getApiEndpoint($store);
        $apiSecret   = $configuration->getApiSecret($store);

        return PrismicApi::get(
            $apiEndpoint,
            $apiSecret,
            null,
            $this->cacheProxy
        );
    }
}