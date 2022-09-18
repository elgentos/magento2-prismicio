<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

/**
 * Class LinkWithTrailingSlash
 * @package Elgentos\PrismicIO\Block\Dom
 *
 * @deprecated we have deprecated this feature because you can now just add a plugin which adds these trailing slashes for all Prismic links
 * @see \Elgentos\PrismicIO\Plugin\ViewModel\LinkResolver\AppendTrailingSlashes
 */
class LinkWithTrailingSlash extends Link
{
    public function fetchDocumentView(): string
    {
        return $this->escapeUrl(rtrim(parent::fetchDocumentView(), '/') . '/');
    }
}
