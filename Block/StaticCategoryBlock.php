<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Magento\Catalog\Model\Layer\Resolver;
use stdClass;

class StaticCategoryBlock extends AbstractBlock
{
    /** @var Api */
    private $api;

    /** @var string */
    private $contentType;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver $linkResolver
     * @param Api $api
     * @param Resolver $layerResolver
     * @param string $contentType
     * @param array $data
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Api $api,
        Resolver $layerResolver,
        string $contentType = 'static_block',
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->api = $api;
        $this->layerResolver = $layerResolver;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->createPrismicDocument();
        return parent::_toHtml();
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createPrismicDocument()
    {
        $data = $this->getData('data') ?? [];
        if (! (isset($this->contentType))) {
            return;
        }

        $document = new stdClass();
        $options  = $this->api->getOptions();

        $document->uid  = $this->getCurrentCategoryUID();
        $document->type = $data['content_type'] ?? $this->contentType;
        $document->lang = $data['lang'] ??  $options['lang'];

        $this->setDocument($document);
    }

    /**
     * @return string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function fetchDocumentView(): string
    {
        if (! $this->fetchChildDocument()) {
            return '';
        }

        $html = '';
        foreach ($this->getChildNames() as $childName) {
            $useCache = ! $this->updateChildDocumentWithDocument($childName);
            $html    .= $this->getChildHtml($childName, $useCache);
        }

        return $html;
    }

    public function getCurrentCategoryUID(): String
    {
       return 'category-' . $this->layerResolver->get()->getCurrentCategory()->getId();
    }

    /**
     * @return bool
     * @throws \Elgentos\PrismicIO\Exception\ApiNotEnabledException
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function fetchChildDocument(): bool
    {
        $context = $this->getContext();

        // We need to update the document to the current context to change scope for children
        $this->setDocument($context);

        $uid  = $this->getCurrentCategoryUID() ?? '';
        $type = $context->type ?? '';

        $document = $this->api->getDocumentByUid($uid, $type, ['lang' => $context->lang]);
        if (! $document) {
            return false;
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);

        return true;
    }
}
