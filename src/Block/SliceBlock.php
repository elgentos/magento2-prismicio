<?php

namespace Elgentos\PrismicIO\Block;

class SliceBlock extends AbstractBlock
{
    protected function _toHtml(): string
    {
        return $this->fetchDocumentView();
    }

    public function fetchDocumentView(): string
    {
        $reference = $this->getReference();

        $layout = $this->getLayout();

        $block = $layout->getBlock($reference);
        if (! ($block instanceof BlockInterface)) {
            return '';
        }

        $block->setDocument($this->getDocument());
        return $block->toHtml();
    }
}