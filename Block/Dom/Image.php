<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Image extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();

        $cssClasses = ($this->getData('css_class') ? 'class="'. $this->getData('css_class') .'"' : '');

        return '<img src="' . $context->url . '"
                     '.$cssClasses.'
                     alt="' . $context->alt . '"
                     width="'. $context->dimensions->width .'"
                     height="'. $context->dimensions->height .'" />';
    }
}
