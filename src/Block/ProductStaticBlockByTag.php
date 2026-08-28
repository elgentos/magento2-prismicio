<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Resolves its document by matching a tag against the currently viewed product - default
 * convention is "product_sku_{sku}" or "product_id_{id}", either one matches. Override
 * resolveTag() for a different convention.
 */
class ProductStaticBlockByTag extends StaticBlockByTag
{
    public function __construct(
        Context                   $context,
        DocumentResolver          $documentResolver,
        LinkResolver               $linkResolver,
        Api                        $api,
        StoreManagerInterface      $storeManager,
        private readonly Registry  $registry,
        string                     $contentType = 'static_block',
        ?string                    $identifier = null,
        array                      $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $api, $storeManager, $contentType, $identifier, $data);
    }

    /**
     * Public so a plugin can get the same product a resolveTag()/buildTag() plugin is reacting
     * to, without re-deriving it from the registry itself.
     */
    public function getCurrentProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    public function resolveTag(): string|array|null
    {
        $product = $this->getCurrentProduct();

        return $product ? $this->buildTag($product) : null;
    }

    /**
     * Builds the tags for a product - by default sku and id, either one matches. Public, and
     * takes the product itself, so a plugin can add its own tags (e.g. a promo-type attribute)
     * without re-deriving the product.
     *
     * @param Product $product
     * @return string[]
     */
    public function buildTag(Product $product): array
    {
        return [
            'product_sku_' . $product->getSku(),
            'product_id_' . $product->getId(),
        ];
    }
}
