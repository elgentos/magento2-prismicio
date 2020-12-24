<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

class Plain extends Raw
{
    /**
     * Fetch the document as plain text
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return $this->_escaper->escapeHtml(parent::fetchDocumentView());
    }
}
