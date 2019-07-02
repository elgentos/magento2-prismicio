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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Api as PrismicApi;

class Api
{

    /** @var ConfigurationInterface */
    private $configuration;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
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
            ->allowDebugInFrontend($this->storeManager->getStore());
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
            $apiSecret
        );
    }
}
