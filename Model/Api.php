<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 21:48
 */

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Model\Api\CacheProxy;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Api as PrismicApi;

class Api
{

    /** @var ConfigurationInterface */
    private $configuration;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var CacheProxy */
    private $cacheProxy;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        CacheProxy $cacheProxy
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->cacheProxy = $cacheProxy;
    }

    /**
     * Tell wheter the API is enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isActive(): bool
    {
        return $this->configuration
                ->getApiEnabled($this->storeManager->getStore());
    }

    /**
     * Tell wetter preview mode is allowed
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isPreviewAllowed(): bool
    {
        return $this->configuration
            ->allowPreviewInFrontend($this->storeManager->getStore());
    }

    /**
     * Is fallback allowed
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isFallbackAllowed(): bool
    {
        return $this->configuration
                ->hasContentLanguageFallback($this->storeManager->getStore());
    }

    /**
     * Get API options
     *
     * @param array $options
     * @return array
     * @throws NoSuchEntityException
     */
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

    /**
     * Get API options with fallback language
     *
     * @param array $options
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOptionsLanguageFallback(array $options = []): array
    {
        $store = $this->storeManager->getStore();

        if (! isset($options['lang']) && $this->configuration->hasContentLanguageFallback($store)) {
            $options['lang'] = $this->configuration->getContentLanguageFallback($store);
        }

        return $this->getOptions($options);
    }

    /**
     * Get default content type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDefaultContentType(): string
    {
        return $this->configuration->getContentType($this->storeManager->getStore());
    }

    /**
     * Create a prismic API for reading content
     *
     * @return PrismicApi
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function create(): PrismicApi
    {
        $configuration = $this->configuration;
        $store = $this->storeManager->getStore();

        if (! $this->isActive()) {
            throw new ApiNotEnabledException;
        }

        $apiEndpoint = $configuration->getApiEndpoint($store);
        $apiSecret = $configuration->getApiSecret($store);

        return PrismicApi::get(
            $apiEndpoint,
            $apiSecret,
            null,
            $this->cacheProxy
        );
    }

    /**
     * Get document by uid
     *
     * @param string $uid
     * @param string|null $contentType
     * @return \stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentByUid(string $uid, string $contentType = null): ?\stdClass
    {
        $contentType = $contentType ?? $this->getDefaultContentType();
        $api = $this->create();

        $allowedContentTypes = $api->getData()
                ->getTypes();
        if (! isset($allowedContentTypes[$contentType])) {
            return null;
        }

        $document = $api->getByUID($contentType, $uid, $this->getOptions());
        if ($document || ! $this->api->isFallbackAllowed()) {
            return $document;
        }

        return $api->getByUID($contentType, $uid, $this->getOptionsLanguageFallback());
    }

    /**
     * Get document by id
     *
     * @param string $id
     * @return \stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentById(string $id): ?\stdClass
    {
        $api = $this->create();
        return $api->getByID($id, $this->getOptions());
    }

}
