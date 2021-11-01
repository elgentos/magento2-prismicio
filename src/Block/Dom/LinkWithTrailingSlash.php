<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

/**
 * @deprecated use the provided plugin for this instead
 * @see \Elgentos\PrismicIO\Plugin\ViewModel\LinkResolver\AppendTrailingSlashes
 */
class LinkWithTrailingSlash extends Link
{
    /**
     * Fetch the URL and add a trailing slash
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return $this->_escaper->escapeUrl(
            rtrim(parent::fetchDocumentView(), '/') . '/'
        );
    }
}
