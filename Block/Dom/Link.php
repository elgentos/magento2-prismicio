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
        return $this->_escaper->escapeUrl(
            $this->replaceRelativeUrl(
                $this->getUrlForDocumentView()
            )
        );
    }

    public function getUrlForDocumentView()
    {
        return $this->getUrlWithContext($this->getContext());
    }

    public function getUrlWithContext($context): string
    {
        if (! ($context instanceof \stdClass)) {
            // Sanity check if someone puts the url itself in here
            return $context;
        }

        // Default Prismic + always a string
        return PrismicLink::asUrl($context, $this->getLinkResolver()) ?? '';
    }
}
