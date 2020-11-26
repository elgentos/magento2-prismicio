<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

class Image extends Raw
{
    public function fetchDocumentView(): string
    {
        return '<img src="' . $this->escapeHtml(parent::fetchDocumentView()) . '" alt="' . $this->getAltText() . '" />';
    }
}
