<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

class Image extends Raw
{
    /**
     * Get the document view as an image tag.
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return '<img src="' . $this->_escaper->escapeHtml(parent::fetchDocumentView()) . '"
            alt="' . $this->getData('alt_text') . '" />';
    }
}
