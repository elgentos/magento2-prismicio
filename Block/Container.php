<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 12:30
 */

namespace Elgentos\PrismicIO\Block;

use \Magento\Framework\View\Layout\Element;

class Container extends AbstractBlock
{
    public function fetchDocumentView(): string
    {
        $context = $this->getContext();

        $html = '';
        $childNames = $this->getChildNames();
        foreach ($childNames as $childName) {
            $itemBlock = $this->getChildBlock($childName);
            $itemBlock->setDocument($context);

            $html .= $itemBlock->toHtml();
        }

        $htmlTag = $this->getData(Element::CONTAINER_OPT_HTML_TAG);
        if ($html == '' || !$htmlTag) {
            return $html;
        }

        $htmlId = $this->getData(Element::CONTAINER_OPT_HTML_ID);
        if ($htmlId) {
            $htmlId = ' id="' . $htmlId . '"';
        }

        $htmlClass = $this->getData(Element::CONTAINER_OPT_HTML_CLASS);
        if ($htmlClass) {
            $htmlClass = ' class="' . $htmlClass . '"';
        }

        return sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $htmlTag, $htmlId, $htmlClass, $html);
    }
}
