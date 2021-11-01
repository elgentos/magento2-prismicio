<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Model\Api\CacheProxy;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Api as PrismicApi;
use stdClass;

class Api
{
    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private CacheProxy $cacheProxy;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        CacheProxy $cacheProxy
    ) {
        $this->configuration = $configuration;
        $this->storeManager  = $storeManager;
        $this->cacheProxy    = $cacheProxy;
    }

    public function isActive(): bool
    {
        return $this->configuration
                ->isApiEnabled($this->storeManager->getStore());
    }

    public function isPreviewAllowed(): bool
    {
        return $this->configuration
            ->allowPreviewInFrontend($this->storeManager->getStore());
    }

    public function isFallbackAllowed(): bool
    {
        return $this->configuration
            ->hasContentLanguageFallback($this->storeManager->getStore());
    }

    public function getDocumentIdInLanguage(string $language, stdClass $document = null): ?string
    {
        $alternateLanguages = (array)($document->alternate_languages ?? []);

        if (empty($alternateLanguages)) {
            return null;
        }

        $availableLanguages = array_filter(
            $alternateLanguages,
            function ($lang) use ($language) {
                return ($lang->lang ?? null) === $language;
            }
        );

        $available = array_shift($availableLanguages);

        if (!$available) {
            return null;
        }

        return $available->id;
    }

    public function getDocumentIdInHomeLanguage(stdClass $document = null): ?string
    {
        if (!$this->isFallbackAllowed()) {
            return null;
        }

        return $this->getDocumentIdInLanguage(
            $this->configuration->getContentLanguage($this->storeManager->getStore()),
            $document
        );
    }

    public function getOptions(array $options = []): array
    {
        $store = $this->storeManager->getStore();

        if (!isset($options['lang'])) {
            $options['lang'] = $this->configuration->getContentLanguage($store);
        }

        if (!isset($options['fetchLinks'])) {
            $options['fetchLinks'] = $this->configuration->getFetchLinks($store);
        }

        return array_filter($options);
    }

    public function getOptionsLanguageFallback(array $options = []): array
    {
        $store = $this->storeManager->getStore();

        if (
            !isset($options['lang']) &&
            $this->configuration->hasContentLanguageFallback($store)
        ) {
            $options['lang'] = $this->configuration
                ->getContentLanguageFallback($store);
        }

        return $this->getOptions($options);
    }

    public function getDefaultContentType(): string
    {
        return $this->configuration
            ->getContentType($this->storeManager->getStore());
    }

    /**
     * @throws ApiNotEnabledException
     */
    public function create(): PrismicApi
    {
        $configuration = $this->configuration;
        $store         = $this->storeManager->getStore();

        if (!$this->isActive()) {
            throw new ApiNotEnabledException();
        }

        $apiEndpoint = $configuration->getApiEndpoint($store);
        $apiSecret   = $configuration->getApiSecret($store);

        return PrismicApi::get(
            $apiEndpoint,
            $apiSecret,
            null,
            $this->cacheProxy
        );
    }

    public function getDocumentByUid(
        string $uid,
        ?string $contentType = null,
        array $options = []
    ): ?stdClass {
        $contentType         = $contentType ?? $this->getDefaultContentType();
        $api                 = $this->create();
        $allowedContentTypes = $api->getData()
            ->getTypes();

        if (!isset($allowedContentTypes[$contentType])) {
            return null;
        }

        $document = $api->getByUID(
            $contentType,
            $uid,
            $this->getOptions($options)
        );

        if ($document || !$this->isFallbackAllowed()) {
            return $document;
        }

        return $api->getByUID(
            $contentType,
            $uid,
            $this->getOptionsLanguageFallback($options)
        );
    }

    public function getSingleton(string $contentType = null, array $options = []): ?stdClass
    {
        $contentType         = $contentType ?? $this->getDefaultContentType();
        $api                 = $this->create();
        $allowedContentTypes = $api->getData()->getTypes();

        if (! isset($allowedContentTypes[$contentType])) {
            return null;
        }

        $document = $api->getSingle($contentType, $this->getOptions($options));

        if ($document || !$this->isFallbackAllowed()) {
            return $document;
        }

        return $api->getSingle($contentType, $this->getOptionsLanguageFallback($options));
    }

    public function getDocumentById(string $id, array $options = []): ?stdClass
    {
        $api = $this->create();

        return $api->getByID($id, $this->getOptions($options));
    }
}
