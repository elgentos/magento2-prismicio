<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Exception\ConfigLanguageFallbackIsNotSetException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration implements ConfigurationInterface
{

    /** @var ScopeConfigInterface */
    private $config;

    /** @var State */
    private $state;

    public function __construct(
        ScopeConfigInterface $config,
        State $state
    ) {
        $this->config = $config;
        $this->state = $state;
    }

    public function getApiEndpoint(StoreInterface $store): string
    {
        return (string)$this->config->getValue(
            self::XML_PATH_API_ENDPOINT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getApiSecret(StoreInterface $store): string
    {
        return (string)$this->config->getValue(
            self::XML_PATH_API_SECRET,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getApiEnabled(StoreInterface $store): bool
    {
        return (bool)$this->config->getValue(
            self::XML_PATH_API_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getContentLanguage(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
            self::XML_PATH_CONTENT_LANGUAGE,
            ScopeInterface::SCOPE_STORE,
            $store
        ) ?? '*');
    }

    public function hasContentLanguageFallback(StoreInterface $store): bool
    {
        return (bool)$this->config->getValue(
                self::XML_PATH_CONTENT_LANGUAGE_FALLBACK,
                ScopeInterface::SCOPE_STORE,
                $store
            );
    }

    public function getContentLanguageFallback(StoreInterface $store): string
    {
        if (! $this->hasContentLanguageFallback($store)) {
            throw new ConfigLanguageFallbackIsNotSetException('No config language fallback is set for this store');
        }

        return (string)($this->config->getValue(
                self::XML_PATH_CONTENT_LANGUAGE_FALLBACK,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '*');
    }

    public function getFetchLinks(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
            self::XML_PATH_CONTENT_FETCHLINKS,
            ScopeInterface::SCOPE_STORE,
            $store
            ) ?? '');
    }

    public function getContentType(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
            self::XML_PATH_CONTENT_CONTENT_TYPE,
            ScopeInterface::SCOPE_STORE,
            $store
            ) ?? '');
    }

    public function allowDebugInFrontend(StoreInterface $store): bool
    {
        // Only allow in developer mode
        if ($this->state->getMode() !== $this->state::MODE_DEVELOPER) {
            return false;
        }

        return (bool)($this->config->getValue(
                self::XML_PATH_CONTENT_ALLOW_DEBUG,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function allowPreviewInFrontend(StoreInterface $store): bool
    {
        return (bool)($this->config->getValue(
                self::XML_PATH_CONTENT_ALLOW_PREVIEW,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function getIntegrationFieldsAccessToken(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
                self::XML_PATH_INTEGRATION_ACCESS_TOKEN,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function getIntegrationFieldsAttributes(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
                self::XML_PATH_INTEGRATION_ATTRIBUTES,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function getIntegrationFieldsVisibility(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
                self::XML_PATH_INTEGRATION_VISIBILITY,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function allowSyncDisabledProducts(StoreInterface $store): bool
    {
        return (bool)($this->config->getValue(
                self::XML_PATH_INTEGRATION_SYNC_DISABLED_PRODUCTS,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }

    public function getSitemapContentTypes(StoreInterface $store): string
    {
        return (string)($this->config->getValue(
                self::XML_PATH_SITEMAP_CONTENT_TYPES,
                ScopeInterface::SCOPE_STORE,
                $store
            ) ?? '');
    }
}
