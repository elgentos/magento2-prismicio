<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Prismic\Dom\RichText as PrismicRichText;

class RichText extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        return PrismicRichText::asHtml($this->getContext(), $this->getLinkResolver());
    }

}