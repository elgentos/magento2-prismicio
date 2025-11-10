<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\SliceNotFoundException;

class Slices extends Group
{
    #[\Override]
    public function fetchItem(\stdClass $slice, $key = null): string
    {
        $sliceTypeBlock = $this->getSliceTypeBlock($slice->slice_type);
        $excludedSlicesFromRender = $this->getData('excluded_slices');

        if (
            null === $sliceTypeBlock &&
            !in_array($slice->slice_type, $excludedSlicesFromRender ?? [])
        ) {
        
            SliceNotFoundException::throwException(
                $this,
                [
                'slice_type' => $slice->slice_type,
                ]
            );

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
