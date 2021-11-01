<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;

class Debug extends AbstractBlock
{
    /** @var ConfigurationInterface */
    private ConfigurationInterface $configuration;

    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;

    /**
     * Constructor.
     *
     * @param Context                $context
     * @param DocumentResolver       $documentResolver
     * @param LinkResolver           $linkResolver
     * @param ConfigurationInterface $configuration
     * @param StoreManagerInterface  $storeManager
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->configuration = $configuration;
        $this->storeManager  = $storeManager;

        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    /**
     * Fetch the debuggers document view
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        if (!$this->configuration->allowDebugInFrontend($this->storeManager->getStore())) {
            // Only allow debug in developer mode
            return '';
        }

        return '<pre>' .
            $this->_escaper->escapeHtml(print_r($this->getContext(), true)) .
            '</pre>';
    }
}
