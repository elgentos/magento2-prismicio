<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:39
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Dom\AbstractDom;
use Prismic\Dom\RichText;

class PageTitle extends AbstractDom
{

    protected function _prepareLayout()
    {
        $this->fetchView(false);

        $this->pageConfig->getTitle()->set(RichText::asText($this->getContext()));
        return $this;
    }

    public function fetchDocumentView(): string
    {
        return '';
    }

}