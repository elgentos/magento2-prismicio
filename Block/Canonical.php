<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends AbstractTemplate
{

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    public function __construct(
        Template\Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->storeManager = $storeManager;
    }

    public function getCanonicalUrl()
    {
        return $this->storeManager->getStore()->getCurrentUrl(false);
    }
}
