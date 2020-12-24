<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\AbstractBlock as FrameworkAbstractBlock;
use Magento\Framework\View\Element\Context;

abstract class AbstractBlock extends FrameworkAbstractBlock implements BlockInterface
{
    use LinkResolverTrait;
    use DocumentResolverTrait;
    use UpdateChildBlockWithDocumentTrait;

    /**
     * Constructor.
     *
     * @param Context          $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver     $linkResolver
     * @param array            $data
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        array $data = []
    ) {
        $this->documentResolver = $documentResolver;
        $this->linkResolver     = $linkResolver;

        parent::__construct($context, $data);
    }

    /**
     * Get reference key
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->_getData(BlockInterface::REFERENCE_KEY) ?:
            // Fallback on template parameter
            $this->_getData('template') ?:
                '*';
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->hasContext()) {
            return '';
        }

        return $this->fetchDocumentView();
    }

    /**
     * Replace the relative URL in Prismic with the actual URL in Magento.
     *
     * @param string $context
     *
     * @return string
     */
    protected function replaceRelativeUrl(string $context): string
    {
        $baseUrl               = $this->_urlBuilder->getBaseUrl();
        $findAndReplaceBaseUrl = [
            'http://{{relative}}/',
            'https://{{relative}}/',
            'http://{{relative}}',
            'https://{{relative}}',
        ];

        return str_replace($findAndReplaceBaseUrl, $baseUrl, $context);
    }

    /**
     * Fetch document view
     *
     * @return string
     */
    abstract public function fetchDocumentView(): string;
}
