<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:34
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\AbstractBlock as FrameworkAbstractBlock;
use Magento\Framework\View\Element\Context;

abstract class AbstractBlock extends FrameworkAbstractBlock implements BlockInterface
{
    use LinkResolverTrait;
    use DocumentResolverTrait;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        array $data = []
    ) {
        $this->documentResolver = $documentResolver;
        $this->linkResolver = $linkResolver;

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
     */
    protected function _toHtml()
    {
        if (! $this->hasContext()) {
            return '';
        }

        return $this->fetchDocumentView();
    }

    /**
     * Fetch document view
     *
     * @return string
     */
    abstract public function fetchDocumentView(): string;
}
