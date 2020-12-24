<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Dom;

use Elgentos\PrismicIO\Block\AbstractBlock;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\HtmlSerializer;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Prismic\Dom\RichText as PrismicRichText;

class RichText extends AbstractBlock
{
    /** @var HtmlSerializer */
    private $htmlSerializer;

    /**
     * Constructor.
     *
     * @param Context          $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver     $linkResolver
     * @param HtmlSerializer   $htmlSerializer
     * @param array            $data
     */
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

    /**
     * Fetch the document as a rich text string and replace relative URLs.
     *
     * @return string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function fetchDocumentView(): string
    {
        $html = PrismicRichText::asHtml(
            $this->getContext(),
            $this->getLinkResolver(),
            [$this->htmlSerializer, 'serialize']
        );

        return $this->replaceRelativeUrl($html);
    }
}
