<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:34
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractTemplate;

abstract class AbstractDom extends AbstractTemplate
{

    public function fetchView($fileName)
    {
        // Mis-use the template param as shorthand to resolve the document reference
        $fileName = $fileName ?: $this->getTemplate();
        $this->setDocumentReference($fileName);

        return $this->fetchDocumentView();
    }

    abstract public function fetchDocumentView(): string;

}