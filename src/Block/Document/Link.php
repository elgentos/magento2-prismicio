<?php

namespace Elgentos\PrismicIO\Block\Document;

use Elgentos\PrismicIO\Block\Dom\Link as DomLink;

class Link extends DomLink
{
    #[\Override]
    public function getUrlForDocumentView(): string
    {
        // Clone object to keep orignal object in place
        $context = clone $this->getContext();
        $context->link_type = 'Document';

        return $this->getUrlWithContext($context);
    }
}
