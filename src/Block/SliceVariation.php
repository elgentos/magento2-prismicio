<?php

namespace Elgentos\PrismicIO\Block;

class SliceVariation extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();
        $variation = $context->variation ?: null;
        if (! $variation) {
            return '';
        }

        $block = $this->getChildBlock($variation);
        if (! ($block instanceof BlockInterface)) {
            return '';
        }

        $block->setDocument($this->getContext());
        return $block->toHtml();
    }
}