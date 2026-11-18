<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Resolves its document by matching a tag against the currently viewed category - default
 * convention is "category_{id}". Override resolveTag() for a different convention, e.g. a
 * custom category attribute.
 */
class CategoryStaticBlockByTag extends StaticBlockByTag
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
     * Public so a plugin can get the same category a resolveTag()/buildTag() plugin is reacting
     * to, without re-deriving it from the registry itself.
     */
    public function getCurrentCategory(): ?Category
    {
        return $this->registry->registry('current_category');
    }

    public function resolveTag(): string|array|null
    {
        $category = $this->getCurrentCategory();

        return $category ? $this->buildTag($category) : null;
    }

    /**
     * Builds the tag(s) for a category. Public, and takes the category itself, so a plugin can
     * add its own tags (e.g. from a custom attribute) without re-deriving the category.
     *
     * @param Category $category
     * @return string|string[]
     */
    public function buildTag(Category $category): string|array
    {
        return 'category_' . $category->getId();
    }
}
