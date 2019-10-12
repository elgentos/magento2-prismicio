<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Email\Model\Template\Filter;
use Magento\Framework\View\Element\Context;

class FilterVars extends AbstractBlock
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * MagentoVars constructor.
     * @param Context $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver $linkResolver
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->filter = $filter;
    }

    /**
     * Fetch document view
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        foreach ($this->getChildNames() as $alias) {
            $this->updateChildDocumentWithContext($alias);
        }

        // Replace
        $html = preg_replace('/{{([^ }]+)}}/', '{{customvar code=\\1}}', $this->getChildHtml());
        return $this->filter->filter($html);
    }
}
