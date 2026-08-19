<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\ContentRelationshipNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;

class ContentRelationship extends AbstractBlock
{
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        private readonly Api $api,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    public function fetchDocumentView(): string
    {
        $link = $this->getContext();
        if (empty($link->id)) {
            return '';
        }

        $document = $this->api->getDocumentById($link->id);
        if (!$document) {
            return '';
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';

        $typeBlock = $this->getDocumentTypeBlock($document->type);
        if (!$typeBlock) {
            ContentRelationshipNotFoundException::throwException(
                $this,
                [
                    'document_type' => $document->type,
                ]
            );

            return '';
        }

        $typeBlock->setDocument($document);
        return $typeBlock->toHtml();
    }

    public function getDocumentTypeBlock(string $type): ?BlockInterface
    {
        $child = $this->getChildBlock($type) ?: $this->getChildBlock($this->getNameInLayout() . '.' . $type);
        return $child ?: null;
    }
}
