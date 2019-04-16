<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;


use Prismic\Dom\Link as PrismicLink;

class Link extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        return PrismicLink::asUrl($this->getContext(), $this->getLinkResolver());
    }

}