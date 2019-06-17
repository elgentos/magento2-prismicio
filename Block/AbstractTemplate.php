<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 21:04
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;

abstract class AbstractTemplate extends Template implements BlockInterface
{
    use LinkResolverTrait;
    use DocumentResolverTrait;

    public function __construct(
        Template\Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        array $data = []
    ) {
        $this->linkResolver = $linkResolver;
        $this->documentResolver = $documentResolver;

        parent::__construct($context, $data);
    }

    public function getChildHtml($alias = '', $useCache = true)
    {
        $this->updateChildDocument($alias);
        return parent::getChildHtml($alias, $useCache);
    }

    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function updateChildDocument(string $alias): void
    {
        $block = $this->getChildBlock($alias);
        if (! $block) {
            return;
        }

        $block->setDocument($this->getContext());
    }
}
