<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\SliceNotFoundException;

class Slices extends Group
{
    public function fetchItem(\stdClass $slice, $key = null): string
    {
        $sliceTypeBlock = $this->getSliceTypeBlock($slice->slice_type);
        if (null === $sliceTypeBlock) {
            SliceNotFoundException::throwException($this, [
                'slice_type' => $slice->slice_type,
            ]);
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
