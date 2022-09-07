<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Context;
use Magento\Catalog\Model\Layer\Resolver;
use stdClass;

class StaticCategoryBlock extends StaticBlock
{

    /** @var Resolver */
    private $layerResolver;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param DocumentResolver $documentResolver
     * @param LinkResolver $linkResolver
     * @param Api $api
     * @param string $contentType
     * @param string|null $identifier
     * @param array $data
     * @param Resolver $layerResolver
     */
    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        Api $api,
        string $contentType = 'static_block',
        string $identifier = null,
        array $data = [],
        Resolver $layerResolver
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $api, $contentType, $identifier, $data);
        $this->layerResolver = $layerResolver;
    }


    protected function getDocumentUID(): string {
        try {
            return sprintf('category-%u', $this->layerResolver->get()->getCurrentCategory()->getId());

        } catch(\Exception $exception) {
            return parent::getDocumentUID();
        }
    }

}

