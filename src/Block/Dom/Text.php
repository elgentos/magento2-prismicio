<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Prismic\Dom\RichText;

class Text extends AbstractBlock
{
    /**
     * Fetch the document as plain text
     *
     * @return string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function fetchDocumentView(): string
    {
        return $this->_escaper->escapeHtml(RichText::asText($this->getContext()));
    }
}
