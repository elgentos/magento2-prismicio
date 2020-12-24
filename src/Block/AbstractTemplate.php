<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;

abstract class AbstractTemplate extends Template implements BlockInterface
{
    use LinkResolverTrait;
    use DocumentResolverTrait;
    use UpdateChildBlockWithDocumentTrait;

    /**
     * Constructor.
     *
     * @param Template\Context $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver     $linkResolver
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        array $data = []
    ) {
        $this->linkResolver     = $linkResolver;
        $this->documentResolver = $documentResolver;

        parent::__construct($context, $data);
    }

    /**
     * Get the child HTML.
     *
     * @param string $alias
     * @param bool   $useCache
     *
     * @return string
     */
    public function getChildHtml($alias = '', $useCache = true): string
    {
        if ($this->updateChildDocumentWithContext($alias)) {
            $useCache = false;
        }

        return parent::getChildHtml($alias, $useCache);
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (! $this->hasContext()) {
            return '';
        }

        return parent::_toHtml();
    }
}
