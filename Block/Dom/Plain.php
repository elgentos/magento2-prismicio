<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block\Dom;

class Plain extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        return $this->getContext() . '';
    }

}