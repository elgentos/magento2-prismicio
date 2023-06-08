<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Psr\Log\LoggerInterface as Logger;
use stdClass;

class StaticBlock extends AbstractBlock
{
    private Api $api;

    private Logger $logger;

    private string $contentType;

    private ?string $identifier;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Api $api,
        Logger $logger,
        string $contentType = 'static_block',
        string $identifier = null,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->api = $api;
        $this->logger = $logger;
        $this->contentType = $contentType;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
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
    private function createPrismicDocument(): void
    {
        $data = $this->getData('data') ?? [];
        if (! (isset($this->contentType, $this->identifier) || isset($data['uid']) || isset($data['identifier']))) {
            return;
        }

        $document = new stdClass();
        $options  = $this->api->getOptions();

        $document->uid  = $data['uid'] ?? $data['identifier'] ?? $this->identifier;
        $document->type = $data['content_type'] ?? $this->contentType;
        $document->lang = $data['lang'] ??  $options['lang'];

        $this->setDocument($document);
    }

    /**
     * @return string
     * @throws ContextNotFoundException
     */
    public function fetchDocumentView(): string
    {
        try {
            if (!$this->fetchChildDocument()) {
                return '';
            }
        } catch (ApiNotEnabledException|ContextNotFoundException|DocumentNotFoundException|NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            return '';
        }

        $html = '';
        foreach ($this->getChildNames() as $childName) {
            try {
                $useCache = ! $this->updateChildDocumentWithDocument($childName);
                $html    .= $this->getChildHtml($childName, $useCache);
            } catch (DocumentNotFoundException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
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

        $uid  = $context->uid ?? '';
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
