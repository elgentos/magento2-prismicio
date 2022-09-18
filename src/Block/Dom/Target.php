<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;

/**
 * Class Target
 * @package Elgentos\PrismicIO\Block\Dom
 *
 * This class provides functionality for retrieving the target of a link within a PrismicIO context.
 *
 */
class Target extends AbstractBlock
{
    /**
     * @throws ContextNotFoundException|DocumentNotFoundException
     */
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();

        if (!property_exists($context, 'target')) {
            return '';
        }

        return $context->target;
    }
}
