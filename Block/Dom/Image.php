<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Image extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();

        return '<img src="' . $context->url . '"
                     alt="' . $context->alt . '"
                     width="'. $context->dimensions->width .'"
                     heigh="'. $context->dimensions->width .'" />';
    }
}
