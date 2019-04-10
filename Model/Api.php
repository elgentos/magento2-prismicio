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
    )
    {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
    }

    /**
     * Tell wheter the API is enabled
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isActive(): bool
    {
        return $this->configuration
                ->getApiEnabled($this->storeManager->getStore());
    }

    /**
     * Get content language for api
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLanguage(): string
    {
        return $this->configuration
                ->getContentLanguage($this->storeManager->getStore());
    }

    /**
     * Create a prismic API for reading content
     *
     * @return PrismicApi
     * @throws ApiNotEnabledException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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