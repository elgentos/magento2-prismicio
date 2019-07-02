<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;

class Document extends AbstractBlock implements BlockInterface
{

    /** @var Api */
    private $api;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Api $api,
        array $data = []
    ) {
        $this->api = $api;
        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }


    /**
     * @inheritDoc
     */
    public function fetchDocumentView(): string
    {
        $this->fetchChildDocument();

        $html = '';
        foreach ($this->getChildNames() as $childName) {
            $this->updateChildDocument($childName);
            $html .= $this->getChildHtml($childName, false);
        }

        return $html;
    }

    private function fetchChildDocument(): void
    {
        $context = $this->getContext();

        $isBroken = (bool)($context->isBroken ?? true);

        if ($isBroken) {
            // We can only query existing pages
            return;
        }
        $options = $this->api->getOptions();

        $id = $context->id ?? '';
        $options['lang'] = $context->lang;

        $api = $this->api->create();

        $document = $api->getByID($id, $options);
        if (! $document) {
            return;
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);
    }

    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     * @return bool
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function updateChildDocument(string $alias): bool
    {
        $block = $this->getChildBlock($alias);
        if (! $block) {
            return false;
        }

        $block->setDocument($this->getDocument());
        return true;
    }
}
