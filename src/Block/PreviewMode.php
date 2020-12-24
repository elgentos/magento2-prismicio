<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManager;

class PreviewMode extends Template
{
    /** @var ConfigurationInterface */
    private $configuration;

    /**  @var StoreManager */
    private $storeManager;

    /**
     * Constructor.
     * @param Template\Context       $context
     * @param ConfigurationInterface $configuration
     * @param StoreManager           $storeManager
     * @param array                  $data
     */
    public function __construct(
        Template\Context $context,
        ConfigurationInterface $configuration,
        StoreManager $storeManager,
        array $data = []
    ) {
        $this->configuration = $configuration;
        $this->storeManager  = $storeManager;

        parent::__construct($context, $data);
    }

    /**
     * Return the HTML for the given store
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function _toHtml()
    {
        if ($this->configuration->allowPreviewInFrontend($this->storeManager->getStore())) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get the API endpoint
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getApiEndpoint(): string
    {
        return $this->configuration->getApiEndpoint($this->storeManager->getStore());
    }
}