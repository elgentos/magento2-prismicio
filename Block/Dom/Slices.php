<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 16-4-19
 * Time: 15:56
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\BlockInterface;

class Slices extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        $slices = $this->getContext();
        if (! is_array($slices)) {
            return '';
        }

        $documentResolver = $this->getDocumentResolver();
        $baseReference = $this->_getData('template');

        $html = '';
        foreach ($slices as $index => $slice) {
            $sliceReference = $baseReference
                    . $documentResolver::CONTEXT_DELIMITER
                    . $index;
            $html .= $this->fetchSlice($slice, $sliceReference);
        }

        return $html;
    }

    public function fetchSlice(\stdClass $slice, string $reference): string
    {
        $sliceTypeBlock = $this->getSliceTypeBlock($slice->slice_type);
        if (null === $sliceTypeBlock) {
            return '';
        }

        $sliceTypeBlock->setData(BlockInterface::REFERENCE_KEY, $reference);
        return $sliceTypeBlock->toHtml();
    }

    public function getSliceTypeBlock($sliceType): ?BlockInterface
    {
        $childReference = $this->getNameInLayout() . '.' . $sliceType;
        return $this->getChildBlock($childReference) ?: null;
    }

}
