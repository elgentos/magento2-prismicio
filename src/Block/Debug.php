<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;

class Debug extends AbstractBlock
{

    /** @var ConfigurationInterface */
    private $configuration;
    /** var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;

        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    public function fetchDocumentView(): string
    {
        if (! $this->configuration->allowDebugInFrontend($this->storeManager->getStore())) {
            // Only allow debug in developer mode
            return '';
        }

        return '<pre>' . $this->escapeHtml(print_r($this->getContext(), true)) . '</pre>';
    }
}
