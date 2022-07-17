<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 22:43
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\HtmlSerializer;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Prismic\Dom\RichText as PrismicRichText;

class RichText extends AbstractBlock
{
    /**
     * @var HtmlSerializer
     */
    private $htmlSerializer;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        HtmlSerializer $htmlSerializer,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->htmlSerializer = $htmlSerializer;
    }

    public function fetchDocumentView(): string
    {
        $html = PrismicRichText::asHtml(
            $this->getContext(),
            $this->getLinkResolver(),
            $this->getHtmlSerializer() ?? fn($object, string $content) => $this->htmlSerializer->serialize($object, $content)
        );

        return $this->replaceRelativeUrl($html);
    }
}
