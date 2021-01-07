<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use stdClass;

class Group extends AbstractBlock
{
    /**
     * Fetch the document view of the group
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        $items = $this->getContext();

        if (!is_array($items)) {
            return '';
        }

        $html = '';

        foreach ($items as $item) {
            $html .= $this->fetchItem($item);
        }

        return $html;
    }

    /**
     * Fetch the child items.
     *
     * @param stdClass $item
     *
     * @return string
     */
    public function fetchItem(stdClass $item): string
    {
        $childNames = $this->getChildNames();
        $html       = '';

        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setData('document', $item);

            $html .= $itemBlock->toHtml();
        }

        return $html;
    }
}
