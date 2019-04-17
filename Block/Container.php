<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 12:30
 */

namespace Elgentos\PrismicIO\Block;


class Container extends AbstractBlock
{

    public function fetchDocumentView(): string
    {
        $context = $this->getContext();
        $childNames = $this->getChildNames();

        $html = '';
        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setDocument($context);

            $html .= $itemBlock->toHtml();
        }

        return $html;
    }

}