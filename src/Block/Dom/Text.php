<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Prismic\Dom\RichText;

class Text extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        return $this->escapeHtml(RichText::asText($this->getContext()));
    }
}
