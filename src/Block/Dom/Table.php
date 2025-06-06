<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Elgentos\PrismicIO\Helper\CreateTableLayout;

class Table extends AbstractBlock
{
    private CreateTableLayout $tableHelper;
    
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        CreateTableLayout $tableHelper,
        array $data = []
    ) {
        $this->tableHelper = $tableHelper;
        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    public function fetchDocumentView(): string
    {
        $document = json_decode(json_encode($this->getDocument()), true);

        if (!isset($document['primary']['table'])) {
            return json_encode($document);
        }

        if (isset($document['primary']['table']['head']['rows'])) {
            foreach ($document['primary']['table']['head']['rows'] as &$row) {
                foreach ($row['cells'] as &$cell) {
                    $cell['html'] = $this->tableHelper->convertContentWithSpansToHtml($cell['content'] ?? []);
                }
            }
        }

        if (isset($document['primary']['table']['body']['rows'])) {
            foreach ($document['primary']['table']['body']['rows'] as &$row) {
                foreach ($row['cells'] as &$cell) {
                    $cell['html'] = $this->tableHelper->convertContentWithSpansToHtml($cell['content'] ?? []);
                }
            }
        }

        return json_encode($document);
    }
}
