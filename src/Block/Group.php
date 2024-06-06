<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 12:30
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\GroupExpectsAnArrayException;

class Group extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $items = $this->getContext();
        if (! is_array($items)) {
            GroupExpectsAnArrayException::throwException($this);
            return '';
        }

        $html = '';
        foreach ($items as $key => $item) {
            $html .= $this->fetchItem($item, $key);
        }

        return $html;
    }

    public function fetchItem(\stdClass $item, $key = null): string
    {
        $childNames = $this->getChildNames();

        $html = '';
        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setDocument($item);
            if (null !== $key) {
                $itemBlock->setIterator($key);
            }

            $html .= $itemBlock->toHtml();
        }

        return $html;
    }
}
