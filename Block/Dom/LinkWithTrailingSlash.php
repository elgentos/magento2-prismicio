<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

class LinkWithTrailingSlash extends Link
{
    public function fetchDocumentView(): string
    {
        return $this->escapeUrl(rtrim(parent::fetchDocumentView(), '/') . '/');
    }
}
