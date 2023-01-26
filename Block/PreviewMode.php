<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManager;

class PreviewMode extends Template
{
    public const PRISMICIO_PREVIEW_URL
            = 'https://static.cdn.prismic.io/prismic.js?new=true&repo=';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var StoreManager
     */
    private $storeManager;

    public function __construct(
        Template\Context $context,
        ConfigurationInterface $configuration,
        StoreManager $storeManager,
        array $data = []
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (! $this->configuration->allowPreviewInFrontend($this->storeManager->getStore())) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getApiEndpoint(): string
    {
        return $this->configuration->getApiEndpoint($this->storeManager->getStore());
    }

    public function getRepoName(): string
    {
        return str_replace(['http://', 'https://', 'prismic.io', '.', '/api/v2', '/api/v1', '/'], '', $this->getApiEndpoint());
    }

    public function getPreviewUrl(): string
    {
        return self::PRISMICIO_PREVIEW_URL;
    }
}
