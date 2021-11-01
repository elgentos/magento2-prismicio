<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Raw extends AbstractBlock
{
    /**
     * Return the document view as a plain text string.
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return (string) $this->getContext();
    }
}
