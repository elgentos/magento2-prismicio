<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Prismic\Dom\Link as PrismicLink;

class ClickableLink extends Link
{
    public function fetchDocumentView(): string
    {
        return '<a href="' . parent::fetchDocumentView() . '">' . ($this->getLinkTitle() ?: 'Click here') . '</a>';
    }
}
