<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManager;

class PreviewMode extends Template
{

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
}
