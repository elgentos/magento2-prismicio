<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 16-4-19
 * Time: 15:56
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Block\BlockInterface;

class Slices extends AbstractBlock
{

    public function fetchDocumentView(): string
    {
        $slices = $this->getContext();
        if (! is_array($slices)) {
            return '';
        }

        $html = '';
        foreach ($slices as $slice) {
            $html .= $this->fetchSlice($slice);
        }

        return $html;
    }

    public function fetchSlice(\stdClass $slice): string
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
