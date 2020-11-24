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
     * Get document id for the alternate language
     *
     * @param string $language
     * @param \stdClass $document
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDocumentIdInLanguage(string $language, \stdClass $document = null): ?string
    {
        $alternateLanguages = (array)($document->alternate_languages ?? []);
        if (empty($alternateLanguages)) {
            return null;
        }

        $availableLanguages = array_filter($alternateLanguages, function($lang) use ($language) {
            return ($lang->lang ?? null) === $language;
        });

        $available = array_shift($availableLanguages);
        if (! $available) {
            return null;
        }

        return $available->id;
    }

    /**
     * Get document id for fallback language
     *
     * @param \stdClass|null $document
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getDocumentIdInFallbackLanguage(\stdClass $document = null): ?string
    {
        if (! $this->isFallbackAllowed()) {
            return null;
        }

        return $this->getDocumentIdInLanguage(
                $this->configuration->getContentLanguageFallback($this->storeManager->getStore()),
                $document
        );
    }

    /**
     * Get document id for home language
     *
     * @param \stdClass|null $document
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getDocumentIdInHomeLanguage(\stdClass $document = null): ?string
    {
        if (! $this->isFallbackAllowed()) {
            return null;
        }

        return $this->getDocumentIdInLanguage(
                $this->configuration->getContentLanguage($this->storeManager->getStore()),
                $document
        );
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
     * @param array $options
     * @return \stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentByUid(string $uid, string $contentType = null, array $options = []): ?\stdClass
    {
        $contentType = $contentType ?? $this->getDefaultContentType();
        $api = $this->create();

        $allowedContentTypes = $api->getData()
                ->getTypes();
        if (! isset($allowedContentTypes[$contentType])) {
            return null;
        }

        $document = $api->getByUID($contentType, $uid, $this->getOptions($options));
        if ($document || ! $this->isFallbackAllowed()) {
            return $document;
        }

        return $api->getByUID($contentType, $uid, $this->getOptionsLanguageFallback($options));
    }

    /**
     * Get document by uid
     *
     * @param string|null $contentType
     * @param array $options
     * @return \stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getSingleton(string $contentType = null, array $options = []): ?\stdClass
    {
        $contentType = $contentType ?? $this->getDefaultContentType();
        $api = $this->create();

        $allowedContentTypes = $api->getData()
                ->getTypes();
        if (! isset($allowedContentTypes[$contentType])) {
            return null;
        }

        $document = $api->getSingle($contentType, $this->getOptions($options));
        if ($document || ! $this->isFallbackAllowed()) {
            return $document;
        }

        return $api->getSingle($contentType, $this->getOptionsLanguageFallback($options));
    }

    /**
     * Get document by id
     *
     * @param string $id
     * @param array $options
     * @return \stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentById(string $id, array $options = []): ?\stdClass
    {
        $api = $this->create();
        return $api->getByID($id, $this->getOptions($options));
    }

}
