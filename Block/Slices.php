<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 16-4-19
 * Time: 15:56
 */

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
        $childReference = $this->getNameInLayout() . '.' . $sliceType;
        return $this->getChildBlock($childReference) ?: null;
    }
}
