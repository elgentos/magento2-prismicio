<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use stdClass;

class StaticBlock extends AbstractBlock
{
    /** @var Api */
    protected $api;

    /** @var string */
    protected $contentType;

    /** @var string|null */
    protected $identifier;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver $linkResolver
     * @param Api $api
     * @param string $contentType
     * @param string|null $identifier
     * @param array $data
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Api $api,
        string $contentType = 'static_block',
        string $identifier = null,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->api = $api;
        $this->contentType = $contentType;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->createPrismicDocument();
        return parent::_toHtml();
    }

    protected function getDocumentUID(): string {
        $data = $this->getData('data') ?? [];

        return $data['uid'] ?? $data['identifier'] ?? $this->identifier;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createPrismicDocument()
    {
        $document = new stdClass();
        $document->uid = $this->getDocumentUID();

        if (!isset($this->contentType) || empty($document->uid)) {
            return;
        }

        $options  = $this->api->getOptions();

        $data = $this->getData('data') ?? [];
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


        $uid  = $context->uid ?? $this->getDocumentUID() ?? '';
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
