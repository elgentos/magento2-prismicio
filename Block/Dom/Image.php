<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Image extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();
        
        if (!isset($context->url)) {
            return null;
        }

        $cssClasses = $this->getData('css_class') ? 'class="'. $this->getData('css_class') .'"' : '';
        
        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" %s />',
            $context->url,
            $context->alt ?? '',
            $context->dimensions->width,
            $context->dimensions->height,
            $cssClasses
        );
    }
}
