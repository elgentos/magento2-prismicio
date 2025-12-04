<?php


namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Context;

class Document extends AbstractBlock implements IdentityInterface
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
        if (! $this->fetchChildDocument()) {
            return '';
        }

        $html = '';
        foreach ($this->getChildNames() as $childName) {
            $useCache = !$this->updateChildDocumentWithDocument($childName);
            $html .= $this->getChildHtml($childName, $useCache);
        }

        return $html;
    }

    private function fetchChildDocument(): bool
    {
        $context = $this->getContext();

        // We need to update the document to the current context to change scope for children
        $this->setDocument($context);

        $isBroken = (bool)($context->isBroken ?? true);
        if ($isBroken) {
            // We can only query existing pages
            return false;
        }

        $id = $context->id ?? '';

        $options = ['lang' => $context->lang];

        $document = $this->api->getDocumentById($id, $options);
        if ($id = $this->api->getDocumentIdInHomeLanguage($document)) {
            $document = $this->api->getDocumentById($id);
        }

        if (! $document) {
            return false;
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);

        return true;
    }

    /**
     * Get cache key for this block
     *
     * @return string|null
     */
    public function getCacheKey(): ?string
    {
        // Disable caching in preview mode
        if ($this->getRequest()->getParam('token')) {
            return null;
        }

        try {
            $context = $this->getContext();
            $documentId = $context->id ?? '';
            $lang = $context->lang ?? '';
            $store = $this->_storeManager->getStore();

            return 'prismic_document_' . $documentId . '_' . $lang . '_' . $store->getId();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get cache lifetime in seconds
     *
     * @return int|null
     */
    public function getCacheLifetime(): ?int
    {
        // Disable caching in preview mode
        if ($this->getRequest()->getParam('token')) {
            return null;
        }

        // Cache for 1 hour
        return 3600;
    }

    /**
     * Get identities for cache invalidation
     *
     * @return array
     */
    public function getIdentities(): array
    {
        try {
            $context = $this->getContext();
            $documentId = $context->id ?? '';

            return [
                'PRISMICIO_API',
                'PRISMICIO_DOC_' . $documentId
            ];
        } catch (\Exception $e) {
            return ['PRISMICIO_API'];
        }
    }
}
