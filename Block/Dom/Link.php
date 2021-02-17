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
        $context = $this->getContext();
        if (!isset($context->link_type)) {
            $context->link_type = 'Document';
        }
        $url = PrismicLink::asUrl($context, $this->getLinkResolver() ?? '');

        if(!$url) {
            return '';
        }

        return $this->escapeUrl($this->replaceRelativeUrl($url));
    }
}
