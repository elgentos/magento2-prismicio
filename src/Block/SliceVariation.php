<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\SliceVariationNotFoundException;
use Elgentos\PrismicIO\Block\Exception\SliceVariationNoVariationException;

class SliceVariation extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();
        $variation = $context->variation ?: null;
        if (! $variation) {
            SliceVariationNoVariationException::throwException($this);
            return '';
        }

        $block = $this->getChildBlock($variation);
        if (! ($block instanceof BlockInterface)) {
            SliceVariationNotFoundException::throwException(
                $this,
                [
                    'variation' => $variation,
                ],
            );

            return '';
        }

        $block->setDocument($this->getContext());
        return $block->toHtml();
    }
}