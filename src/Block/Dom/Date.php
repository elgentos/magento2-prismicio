<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use IntlDateFormatter;
use Prismic\Dom\Date as PrismicDate;

class Date extends AbstractBlock
{
    /**
     * Get the document view as a formatted string.
     *
     * @return string
     */
    public function fetchDocumentView(): string
    {
        return $this->_localeDate->formatDate(
            PrismicDate::asDate($this->getContext()),
            $this->getData('format') ?: IntlDateFormatter::LONG,
            (bool) $this->getData('showTime')
        );
    }
}
