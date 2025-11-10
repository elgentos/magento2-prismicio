<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManager;

class PreviewMode extends Template
{
    public const PRISMICIO_PREVIEW_URL
            = 'https://static.cdn.prismic.io/prismic.js?new=true&repo=';

    public function __construct(
        Template\Context $context,
        private readonly ConfigurationInterface $configuration,
        private readonly StoreManager $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    #[\Override]
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
        return str_replace(['http://', 'https://', 'cdn.prismic.io', 'prismic.io', '.', '/api/v2', '/api/v1', '/'], '', $this->getApiEndpoint());
    }

    public function getPreviewUrl(): string
    {
        return self::PRISMICIO_PREVIEW_URL;
    }
}
