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
use Elgentos\PrismicIO\Exception\ApiUnavailableException;
use Elgentos\PrismicIO\Model\Api\CacheProxy;
use Elgentos\PrismicIO\Model\Api\State;
use Elgentos\PrismicIO\Model\Document\CacheManager;
use Exception;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Api as PrismicApi;
use Prismic\Exception\ExceptionInterface as PrismicException;
use Prismic\Predicates;
use Psr\Log\LoggerInterface;
use stdClass;
use Throwable;

class Api
{
    /**
     * Cache key prefix for documents that are looked up by id instead of by uid
     */
    private const CACHE_TYPE_BY_ID = 'by_id';

    /**
     * Cache key prefix for documents that are looked up by tag instead of by uid
     */
    private const CACHE_TYPE_BY_TAG = 'by_tag';

    /**
     * Cached repository tag list - tagged as a prismicio_documents entry so it clears with the
     * rest of the document cache, no separate invalidation needed (see hasTag()).
     */
    private const CACHE_KEY_ALL_TAGS = 'prismicio_all_tags';

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private CacheProxy $cacheProxy;

    private LoggerInterface $logger;

    private CacheManager $cacheManager;

    private State $state;

    private CacheInterface $cache;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        CacheProxy $cacheProxy,
        LoggerInterface $logger,
        CacheManager $cacheManager,
        State $state,
        CacheInterface $cache,
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->cacheProxy = $cacheProxy;
        $this->logger = $logger;
        $this->cacheManager = $cacheManager;
        $this->state = $state;
        $this->cache = $cache;
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
     * @param string         $language
     * @param stdClass|null $document
     *
     * @return string|null
     */
    public function getDocumentIdInLanguage(string $language, ?stdClass $document = null): ?string
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
     * @param stdClass|null $document
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getDocumentIdInFallbackLanguage(?stdClass $document = null): ?string
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
     * @param stdClass|null $document
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getDocumentIdInHomeLanguage(?stdClass $document = null): ?string
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

        if ($this->state->isUnavailable()) {
            // Calling a repository that just failed only costs time, every document again
            throw new ApiUnavailableException('The Prismic API failed recently');
        }

        $apiEndpoint = $configuration->getApiEndpoint($store);
        $apiSecret = $configuration->getApiSecret($store);

        try {
            return PrismicApi::get(
                $apiEndpoint,
                $apiSecret,
                null,
                $this->cacheProxy
            );
        } catch (PrismicException $exception) {
            $this->state->markUnavailable($exception);

            throw $exception;
        }
    }

    /**
     * Get document by uid
     *
     * @param string $uid
     * @param string|null $contentType
     * @param array $options
     * @return stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentByUid(string $uid, ?string $contentType = null, array $options = []): ?stdClass
    {
        $contentType = $contentType ?? $this->getDefaultContentType();
        $language = $this->getLanguage($options);

        $cached = $this->cacheManager->get($contentType, $uid, $language, ...$this->getScope());
        if ($cached !== null) {
            return $cached;
        }

        try {
            $api = $this->create();

            $allowedContentTypes = $api->getData()->getTypes();
            if (! isset($allowedContentTypes[$contentType])) {
                return null;
            }

            $document = $api->getByUID($contentType, $uid, $this->getOptions($options));
            if (! $document && $this->isFallbackAllowed()) {
                $document = $api->getByUID($contentType, $uid, $this->getOptionsLanguageFallback($options));
            }
        } catch (ApiUnavailableException | PrismicException $exception) {
            $this->logApiFailure($exception);

            return null;
        }

        if ($document) {
            $this->cacheManager->set($document, $contentType, $uid, $language, ...$this->getScope());
        }

        return $document;
    }

    /**
     * Get document by uid
     *
     * @param string|null $contentType
     * @param array $options
     * @return stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getSingleton(?string $contentType = null, array $options = []): ?stdClass
    {
        $contentType = $contentType ?? $this->getDefaultContentType();
        $language = $this->getLanguage($options);

        // A singleton has no uid of its own, the content type identifies it
        $cached = $this->cacheManager->get($contentType, $contentType, $language, ...$this->getScope());
        if ($cached !== null) {
            return $cached;
        }

        try {
            $api = $this->create();

            $allowedContentTypes = $api->getData()->getTypes();
            if (! isset($allowedContentTypes[$contentType])) {
                return null;
            }

            try {
                $document = $api->getSingle($contentType, $this->getOptions($options));
            } catch (Exception $e) {
                return null;
            }

            if (! $document && $this->isFallbackAllowed()) {
                $document = $api->getSingle($contentType, $this->getOptionsLanguageFallback($options));
            }
        } catch (ApiUnavailableException | PrismicException $exception) {
            $this->logApiFailure($exception);

            return null;
        }

        if ($document) {
            $this->cacheManager->set($document, $contentType, $contentType, $language, ...$this->getScope());
        }

        return $document;
    }

    /**
     * Get document by id
     *
     * @param string $id
     * @param array $options
     * @return stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentById(string $id, array $options = []): ?stdClass
    {
        $language = $this->getLanguage($options);

        $cached = $this->cacheManager->get(self::CACHE_TYPE_BY_ID, $id, $language, ...$this->getScope());
        if ($cached !== null) {
            return $cached;
        }

        try {
            $document = $this->create()->getByID($id, $this->getOptions($options));
        } catch (ApiUnavailableException | PrismicException $exception) {
            $this->logApiFailure($exception);

            return null;
        }

        if ($document) {
            $this->cacheManager->set($document, self::CACHE_TYPE_BY_ID, $id, $language, ...$this->getScope());
        }

        return $document;
    }

    /**
     * Get the first document of a given content type carrying any of the given tags
     *
     * @param string|string[] $tag
     * @param string $contentType
     * @param array $options
     * @return stdClass|null
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function getDocumentByTag(string|array $tag, string $contentType, array $options = []): ?stdClass
    {
        $tags = array_values(array_filter(is_array($tag) ? $tag : [$tag], [$this, 'hasTag']));
        if (empty($tags)) {
            return null;
        }

        $language = $this->getLanguage($options);

        $cached = $this->cacheManager->get(self::CACHE_TYPE_BY_TAG, $contentType . '_' . implode('_', $tags), $language, ...$this->getScope());
        if ($cached !== null) {
            return $cached;
        }

        try {
            $result = $this->create()->query(
                [Predicates::at('document.type', $contentType), Predicates::any('document.tags', $tags)],
                $this->getOptions($options)
            );
        } catch (ApiUnavailableException | PrismicException $exception) {
            $this->logApiFailure($exception);

            return null;
        }

        $document = $result->results[0] ?? null;
        if ($document) {
            $this->cacheManager->set($document, self::CACHE_TYPE_BY_TAG, $contentType . '_' . implode('_', $tags), $language, ...$this->getScope());
        }

        return $document;
    }

    /**
     * Cheap pre-check before querying for a specific tag match. Fails open (assumes the tag
     * might exist) if the tag list can't be loaded, so a transient failure never hides content.
     *
     * @param string $tag
     * @return bool
     */
    private function hasTag(string $tag): bool
    {
        $tags = $this->getAllTags();

        return $tags === null || in_array($tag, $tags, true);
    }

    /**
     * @return string[]|null
     */
    private function getAllTags(): ?array
    {
        $cached = $this->cache->load(self::CACHE_KEY_ALL_TAGS);
        if ($cached !== false) {
            return json_decode($cached);
        }

        try {
            $api = $this->create();
            $response = $api->getHttpClient()->request('GET', $api->form('tags')->url());
            $tags = json_decode((string) $response->getBody());
        } catch (Throwable $exception) {
            return null;
        }

        if (!is_array($tags)) {
            return null;
        }

        // Tagged as a prismicio_documents entry so the webhook's existing cleanType() call
        // clears it too - no separate invalidation path needed.
        $this->cache->save(json_encode($tags), self::CACHE_KEY_ALL_TAGS, [CacheTypes::TAG_DOCUMENTS], 60 * 60 * 48);

        return $tags;
    }

    /**
     * Language the document is requested in, part of the cache key
     *
     * @param mixed[] $options
     * @return string
     * @throws NoSuchEntityException
     */
    private function getLanguage(array $options = []): string
    {
        return (string) ($this->getOptions($options)['lang'] ?? '');
    }

    /**
     * Store and website the document is requested for, the rest of the cache key
     *
     * @return int[]
     * @throws NoSuchEntityException
     */
    private function getScope(): array
    {
        $store = $this->storeManager->getStore();

        return [(int) $store->getId(), (int) $store->getWebsiteId()];
    }

    /**
     * An unreachable repository means the document cannot be delivered, it is not a fatal error.
     * The response is incomplete though, so it must not be cached as if it were complete.
     *
     * @param Throwable $exception
     * @return void
     */
    private function logApiFailure(Throwable $exception): void
    {
        $this->state->markContentUnavailable();

        $this->logger->debug(
            'Prismic document could not be delivered: ' . $exception->getMessage()
        );
    }
}
