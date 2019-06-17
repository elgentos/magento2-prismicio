<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 12:30
 */

namespace Elgentos\PrismicIO\Block;

class Group extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $items = $this->getContext();
        if (! is_array($items)) {
            return '';
        }

        $html = '';
        foreach ($items as $item) {
            $html .= $this->fetchItem($item);
        }

        return $html;
    }

    public function fetchItem(\stdClass $item): string
    {
        $childNames = $this->getChildNames();

        $html = '';
        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setDocument($item);

            $html .= $itemBlock->toHtml();
        }

        return $html;
    }
}
