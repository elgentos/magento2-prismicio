<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\SliceBlockNotFoundException;

class SliceBlock extends AbstractBlock
{
    #[\Override]
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
            SliceBlockNotFoundException::throwException($this);
            return '';
        }

        $block->setDocument($this->getDocument());
        return $block->toHtml();
    }
}