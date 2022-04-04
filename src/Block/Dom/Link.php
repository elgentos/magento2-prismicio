<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Prismic\Dom\Link as PrismicLink;

class Link extends AbstractBlock
{

    /**
     * Fetch the document view as an escaped URL and solve relative URLs.
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        $url = PrismicLink::asUrl($this->getContext(), $this->getLinkResolver())
            ?? '';

        return $this->_escaper->escapeUrl($this->replaceRelativeUrl($url));
    }
}
