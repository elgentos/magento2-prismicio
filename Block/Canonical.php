<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends AbstractTemplate implements IdentityInterface
{
    /**
     * Get canonical url
     *
     * @return string
     */
    public function getCanonicalUrl(): string
    {
        return $this->getCanonical()['url'];
    }

    /**
     * Get canical url
     *
     * @return array
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function getCanonical(): array
    {
        $link = $this->getContext();

        $link->link_type = 'Document';
        $href = $this->getLinkResolver()
            ->resolve($link);

        return [
            'url' => $href,
            'link' => $link
        ];
    }

    /**
     * Get cache key for this block
     *
     * @return string|null
     */
    public function getCacheKey(): ?string
    {
        // Disable caching in preview mode
        if ($this->getRequest()->getParam('token')) {
            return null;
        }

        try {
            $context = $this->getDocumentResolver()->getContext();
            $documentId = $context->id ?? '';

            return 'prismic_canonical_' . $documentId;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get cache lifetime in seconds
     *
     * @return int|null
     */
    public function getCacheLifetime(): ?int
    {
        // Disable caching in preview mode
        if ($this->getRequest()->getParam('token')) {
            return null;
        }

        // Cache for 1 hour
        return 3600;
    }

    /**
     * Get identities for cache invalidation
     *
     * @return array
     */
    public function getIdentities(): array
    {
        try {
            $context = $this->getDocumentResolver()->getContext();
            $documentId = $context->id ?? '';

            return [
                'PRISMICIO_API',
                'PRISMICIO_DOC_' . $documentId
            ];
        } catch (\Exception $e) {
            return ['PRISMICIO_API'];
        }
    }
}
