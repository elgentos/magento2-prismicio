<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block\Dom;

class Debug extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        return '<pre>' . print_r($this->getContext(), true) . '</pre>';
    }

}