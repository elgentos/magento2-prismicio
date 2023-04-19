<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

class Slices extends Group
{
    public function fetchItem(\stdClass $slice): string
    {
        $sliceTypeBlock = $this->getSliceTypeBlock($slice->slice_type);
        if (null === $sliceTypeBlock) {
            return '';
        }

        $sliceTypeBlock->setDocument($slice);
        return $sliceTypeBlock->toHtml();
    }

    public function getSliceTypeBlock($sliceType): ?BlockInterface
    {
        $child = $this->getChildBlock($sliceType) ?: $this->getChildBlock($this->getNameInLayout() . '.' . $sliceType);
        return $child ?: null;
    }
}
