<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use stdClass;

class Slices extends Group
{
    /**
     * Fetch the slice
     *
     * @param stdClass $item
     *
     * @return string
     */
    public function fetchItem(stdClass $item): string
    {
        $sliceTypeBlock = $this->getSliceTypeBlock($item->slice_type);
        if (null === $sliceTypeBlock) {
            return '';
        }

        $sliceTypeBlock->setDocument($item);

        return $sliceTypeBlock->toHtml();
    }

    /**
     * Get the slice types
     *
     * @param string $sliceType
     *
     * @return AbstractBlock|bool|null
     */
    public function getSliceTypeBlock(string $sliceType)
    {
        $childReference = $this->getNameInLayout() . '.' . $sliceType;

        return $this->getChildBlock($childReference) ?: null;
    }
}
