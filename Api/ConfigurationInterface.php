<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api;

use Magento\Store\Api\Data\StoreInterface;

interface ConfigurationInterface
{
    public const XML_PATH_API_ENABLED = 'prismicio/general/enabled',
                XML_PATH_API_ENDPOINT = 'prismicio/general/enpoint',
                XML_PATH_API_SECRET = 'prismicio/general/token',
                XML_PATH_CONTENT_LANGUAGE = 'prismicio/content/language',
                XML_PATH_CONTENT_LANGUAGE_FALLBACK = 'prismicio/content/language_fallback',
                XML_PATH_CONTENT_FETCHLINKS = 'prismicio/content/fetchlinks',
                XML_PATH_CONTENT_CONTENT_TYPE = 'prismicio/content/content_type',
                XML_PATH_CONTENT_ALLOW_DEBUG = 'prismicio/content/allow_debug',
                XML_PATH_CONTENT_ALLOW_PREVIEW = 'prismicio/content/allow_preview',
                XML_PATH_INTEGRATION_ACCESS_TOKEN = 'prismicio/integration_fields/access_token',
                XML_PATH_INTEGRATION_ATTRIBUTES = 'prismicio/integration_fields/attributes',
                XML_PATH_INTEGRATION_SYNC_DISABLED_PRODUCTS = 'prismicio/integration_fields/sync_disabled_products',
                XML_PATH_INTEGRATION_VISIBILITY = 'prismicio/integration_fields/visibility',
                XML_PATH_WEBHOOK_SECRET = 'prismicio/webhook/secret',
                XML_PATH_SITEMAP_CONTENT_TYPES = 'prismicio/sitemap/content_types',
                XML_PATH_URL_REWRITE_CONTENT_TYPES = 'prismicio/url_rewrites/content_types',
                XML_PATH_CACHE_FLUSH_CONTENT_TYPES = 'prismicio/cache_flush/content_types';

    public function getApiEnabled(StoreInterface $store): bool;
    public function getApiEndpoint(StoreInterface $store): string;
    public function getApiSecret(StoreInterface $store): string;

    public function getContentLanguage(StoreInterface $store): string;

    public function hasContentLanguageFallback(StoreInterface $store): bool;
    public function getContentLanguageFallback(StoreInterface $store): string;

    public function getFetchLinks(StoreInterface $store): string;
    public function getContentType(StoreInterface $store): string;

    public function allowDebugInFrontend(StoreInterface $store): bool;
    public function allowPreviewInFrontend(StoreInterface $store): bool;

    public function getIntegrationFieldsAccessToken(StoreInterface $store): string;
    public function getIntegrationFieldsAttributes(StoreInterface $store): string;
    public function getIntegrationFieldsVisibility(StoreInterface $store): string;
    public function allowSyncDisabledProducts(StoreInterface $store): bool;

    public function getWebhookSecret(StoreInterface $store): string;

    public function getSitemapContentTypes(StoreInterface $store): string;

    public function getUrlRewriteContentTypes(StoreInterface $store): array;

    public function getCacheFlushContentTypes(StoreInterface $store): array;
}
