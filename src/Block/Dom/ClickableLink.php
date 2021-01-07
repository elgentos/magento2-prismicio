<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Prismic\Dom\Link as PrismicLink;

class ClickableLink extends Link
{
    /**
     * Get the document view as a clickable link.
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return '<a href="' . parent::fetchDocumentView() . '">' .
            ($this->getData('link_title') ?: __('Click here')) .
            '</a>';
    }
}
