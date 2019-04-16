<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:34
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\BlockInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;

abstract class AbstractDom extends AbstractBlock implements BlockInterface
{

    /**
     * @var DocumentResolver
     */
    private $documentResolver;
    /**
     * @var LinkResolver
     */
    private $linkResolver;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        array $data = []
    )
    {
        $this->documentResolver = $documentResolver;
        $this->linkResolver = $linkResolver;

        parent::__construct($context, $data);
    }

    public function getLinkResolver(): LinkResolver
    {
        return $this->linkResolver;
    }

    public function getDocumentResolver(): DocumentResolver
    {
        return $this->documentResolver;
    }

    /**
     *
     * @return array|\stdClass|string
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function getContext()
    {
        $reference = $this->_getData(BlockInterface::REFERENCE_KEY) ?:
                    // Fallback on template parameter
                    $this->_getData('template') ?:
                    '*';

        return $this->getDocumentResolver()
                ->getContext($reference);
    }

    protected function _toHtml()
    {
        return $this->fetchDocumentView();
    }

    abstract public function fetchDocumentView(): string;

}