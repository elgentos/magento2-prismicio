<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;

class Raw extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        return $this->getContext() . '';
    }
}
