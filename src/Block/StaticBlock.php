<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\StaticBlockNotFoundException;
use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use stdClass;

class StaticBlock extends AbstractBlock implements IdentityInterface
{
    use DocumentCacheableTrait;

    protected string $contentType;
    private ?string $identifier;

    public function __construct(
        Context                  $context,
        DocumentResolver         $documentResolver,
        LinkResolver             $linkResolver,
        protected readonly Api   $api,
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

        $this->storeManager = $storeManager;
        $this->contentType = $contentType;
        $this->identifier = $identifier;
    }

    /**
     * The document isn't resolved until _toHtml() runs, too late for a cache key - use the
     * statically-known identifier/content type/reference instead.
     */
    public function getCacheKeyInfo()
    {
        $key = parent::getCacheKeyInfo();
        $key[] = $this->contentType;
        $key[] = $this->identifier;
        $key[] = $this->getReference();

        return $key;
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
            $html .= $this->getChildHtml($childName, $useCache);
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
    protected function fetchChildDocument(): bool
    {
        $context = $this->getContext();

        // We need to update the document to the current context to change scope for children
        $this->setDocument($context);

        $uid  = $context->uid ?? '';
        $type = $context->type ?? '';
        $lang = $context->lang ?? '';

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

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);

        return true;
    }
}
