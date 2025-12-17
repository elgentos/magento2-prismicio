<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\StaticBlockNotFoundException;
use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Model\Document\CacheManager;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use stdClass;

class StaticBlock extends AbstractBlock
{
    private string $contentType;
    private ?string $identifier;
    private CacheManager $cacheManager;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context                  $context,
        DocumentResolver         $documentResolver,
        LinkResolver             $linkResolver,
        private readonly Api     $api,
        CacheManager             $cacheManager,
        StoreManagerInterface    $storeManager,
        string                   $contentType = 'static_block',
        ?string                  $identifier = null,
        array                    $data = []
    ) {
        parent::__construct(
            $context,
            $documentResolver,
            $linkResolver,
            $data
        );

        $this->contentType = $contentType;
        $this->identifier = $identifier;
        $this->cacheManager = $cacheManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function _toHtml(): string
    {
        $this->createPrismicDocument();
        return parent::_toHtml();
    }

    /**
     * @throws NoSuchEntityException
     */
    private function createPrismicDocument(): void
    {
        $contentType = $this->contentType;
        $identifier  = $this->identifier;

        // Allow using "template" to reference a document (saves XML)
        $reference = $this->getReference();
        if ($reference !== '*') {
            $this->setReference('*');

            $elements = explode('.', $reference);

            if (count($elements) > 1) {
                [$contentType, $identifier] = $elements;
            } else {
                [$identifier] = $elements;
            }
        }

        $data = $this->getData('data') ?? $this->getData() ?? [];
        if (! ($identifier || isset($data['uid']) || isset($data['identifier']))) {
            return;
        }

        $document = new stdClass;
        $options  = $this->api->getOptions();

        $document->uid  = $data['uid'] ?? $data['identifier'] ?? $identifier;
        $document->type = $data['content_type'] ?? $contentType;
        $document->lang = $data['lang'] ??  $options['lang'];

        $this->setDocument($document);
    }

    /**
     * @throws NoSuchEntityException
     * @throws ApiNotEnabledException
     * @throws DocumentNotFoundException
     * @throws ContextNotFoundException
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
     * @throws ApiNotEnabledException
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     * @throws NoSuchEntityException
     */
    private function fetchChildDocument(): bool
    {
        $context = $this->getContext();

        // We need to update the document to the current context to change scope for children
        $this->setDocument($context);

        $uid  = $context->uid ?? '';
        $type = $context->type ?? '';
        $lang = $context->lang ?? '';

        // Get store and website info
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        $websiteId = (int)$store->getWebsiteId();

        // Try to get document from cache
        $document = $this->cacheManager->get($type, $uid, $lang, $storeId, $websiteId);

        // If not cached, fetch from API and cache it
        if ($document === null) {
            $document = $this->api->getDocumentByUid($uid, $type, ['lang' => $lang]);

            if (! $document) {
                StaticBlockNotFoundException::throwException(
                    $this,
                    [
                        'uid' => $uid,
                        'content_type' => $type,
                        'language' => $lang,
                    ]
                );
                return false;
            }

            // Cache the document for next request
            $this->cacheManager->set($document, $type, $uid, $lang, $storeId, $websiteId);
        }

        if (! $document) {
            return false;
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);

        return true;
    }
}
