<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Prismic\Dom\Link as PrismicLink;

class Link extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $linkResolver = $this->getLinkResolver();

        if ($linkResolver->isTrailingSlashForced() && is_string($this->getContext())) {
            return trim($this->getContext(), '/') . '/';
        }

        return $this->escapeUrl(PrismicLink::asUrl($this->getContext(), $linkResolver));
    }
}
