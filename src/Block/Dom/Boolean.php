<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Boolean extends AbstractBlock
{
    public function fetchDocumentView(): bool
    {
        return (bool) $this->getContext();
    }
}
