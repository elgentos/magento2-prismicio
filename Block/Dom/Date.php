<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Prismic\Dom\Date as PrismicDate;

class Date extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        return $this->_localeDate->formatDate(PrismicDate::asDate($this->getContext()), $this->getData('format') ?: \IntlDateFormatter::LONG, (bool) $this->getData('showTime'));
    }
}
