<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api;

use Magento\Store\Api\Data\StoreInterface;

interface ConfigurationInterface
{
    public const XML_PATH_API_ENABLED               = 'prismicio/general/enabled',
        XML_PATH_API_ENDPOINT                       = 'prismicio/general/enpoint',
        XML_PATH_API_SECRET                         = 'prismicio/general/token',
        XML_PATH_CONTENT_LANGUAGE                   = 'prismicio/content/language',
        XML_PATH_CONTENT_LANGUAGE_FALLBACK          = 'prismicio/content/language_fallback',
        XML_PATH_CONTENT_FETCHLINKS                 = 'prismicio/content/fetchlinks',
        XML_PATH_CONTENT_CONTENT_TYPE               = 'prismicio/content/content_type',
        XML_PATH_CONTENT_ALLOW_DEBUG                = 'prismicio/content/allow_debug',
        XML_PATH_CONTENT_ALLOW_PREVIEW              = 'prismicio/content/allow_preview',
        XML_PATH_INTEGRATION_ACCESS_TOKEN           = 'prismicio/integration_fields/access_token',
        XML_PATH_INTEGRATION_ATTRIBUTES             = 'prismicio/integration_fields/attributes',
        XML_PATH_INTEGRATION_SYNC_DISABLED_PRODUCTS = 'prismicio/integration_fields/sync_disabled_products',
        XML_PATH_INTEGRATION_VISIBILITY             = 'prismicio/integration_fields/visibility';

    /**
     * Check if the API is enabled.
     *
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function isApiEnabled(StoreInterface $store): bool;

    /**
     * Get the API endpoint
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getApiEndpoint(StoreInterface $store): string;

    /**
     * Get the API secret
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getApiSecret(StoreInterface $store): string;

    /**
     * Check if the content language.
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getContentLanguage(StoreInterface $store): string;

    /**
     * Check if there is a content language fallback
     *
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasContentLanguageFallback(StoreInterface $store): bool;

    /**
     * Get the content language fallback
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getContentLanguageFallback(StoreInterface $store): string;

    /**
     * Get the fetch links
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getFetchLinks(StoreInterface $store): string;

    /**
     * Get the content type
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getContentType(StoreInterface $store): string;

    /**
     * Check if debugging is allowed
     *
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function allowDebugInFrontend(StoreInterface $store): bool;

    /**
     * Check if preview is allowed
     *
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function allowPreviewInFrontend(StoreInterface $store): bool;

    /**
     * Get the integration fields access token
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getIntegrationFieldsAccessToken(StoreInterface $store): string;

    /**
     * Get the integration fields attributes
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getIntegrationFieldsAttributes(StoreInterface $store): string;

    /**
     * Get the integration fields visibility
     *
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getIntegrationFieldsVisibility(StoreInterface $store): string;

    /**
     * Allow syncing disabled products to Prismic.io
     *
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function allowSyncDisabledProducts(StoreInterface $store): bool;
}
