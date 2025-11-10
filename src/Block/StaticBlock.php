<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\StaticBlockNotFoundException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;

class StaticBlock extends AbstractBlock
{
    public function __construct(
        Context                  $context,
        DocumentResolver         $documentResolver,
        LinkResolver             $linkResolver,
        private readonly Api     $api,
        private readonly string  $contentType = 'static_block',
        private readonly ?string $identifier = null,
        array                    $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    #[\Override]
    protected function _toHtml(): string
    {
        $this->createPrismicDocument();
        return parent::_toHtml();
    }

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

        // Create a document
        $document = new \stdClass;
        $options  = $this->api->getOptions();

        $document->uid  = $data['uid'] ?? $data['identifier'] ?? $identifier;
        $document->type = $data['content_type'] ?? $contentType;
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

        // Render all children
        $html = '';
        foreach ($this->getChildNames() as $childName) {
            $useCache = ! $this->updateChildDocumentWithDocument($childName);
            $html    .= $this->getChildHtml($childName, $useCache);
        }

        return $html;
    }

    /**
     * @return bool
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
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
            StaticBlockNotFoundException::throwException(
                $this,
                [
                    'uid' => $uid,
                    'content_type' => $type,
                    'language' => $context->lang,
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
