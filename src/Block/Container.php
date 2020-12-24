<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Magento\Framework\View\Layout\Element;

class Container extends AbstractBlock
{
    /**
     * Fetch the container's document view
     *
     * @return string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function fetchDocumentView(): string
    {
        $context    = $this->getContext();
        $html       = '';
        $childNames = $this->getChildNames();

        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setData('document', $context);

            $html .= $itemBlock->toHtml();
        }

        $htmlTag   = $this->getData(Element::CONTAINER_OPT_HTML_TAG);
        $htmlId    = $this->getData(Element::CONTAINER_OPT_HTML_ID);
        $htmlClass = $this->getData(Element::CONTAINER_OPT_HTML_CLASS);

        if ($html == '' || !$htmlTag) {
            return $html;
        }

        if ($htmlId) {
            $htmlId = ' id="' . $htmlId . '"';
        }

        if ($htmlClass) {
            $htmlClass = ' class="' . $htmlClass . '"';
        }

        return sprintf(
            '<%1$s%2$s%3$s>%4$s</%1$s>',
            $htmlTag,
            $htmlId,
            $htmlClass,
            $html
        );
    }
}
