<?php
declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Opts a DocumentResolverTrait block into Magento's block-HTML cache, keyed per document so
 * reused block names don't collide. Consumers must implement IdentityInterface and set
 * $this->storeManager.
 */
trait DocumentCacheableTrait
{
    protected StoreManagerInterface $storeManager;

    private const XML_PATH_ENABLED = 'prismicio/block_cache/enabled';
    private const XML_PATH_LIFETIME = 'prismicio/block_cache/lifetime';

    protected function getCacheLifetime()
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return null;
        }

        return (int) $this->_scopeConfig->getValue(self::XML_PATH_LIFETIME, ScopeInterface::SCOPE_STORE);
    }

    public function getCacheKeyInfo()
    {
        $key = [$this->getNameInLayout(), $this->storeManager->getStore()->getId()];

        $document = $this->resolveDocument();
        if ($document) {
            $key[] = $document->type ?? '';
            $key[] = $document->uid ?? $document->id ?? '';
            $key[] = $document->lang ?? '';
        }

        return $key;
    }

    /**
     * Tags with both the document's own identity and one per Prismic tag it carries, so a
     * tag-based lookup (StaticBlockByTag) invalidates even when a different document takes its
     * place. Must match Controller\Webhook\Cache::purgeCachesForDocuments().
     */
    public function getIdentities(): array
    {
        $document = $this->resolveDocument();
        if (!$document || empty($document->type)) {
            return [];
        }

        $uid = $document->uid ?? $document->id ?? '';
        $identities = ['prismicio_document_' . $document->type . '_' . $uid];

        foreach ($document->tags ?? [] as $tag) {
            $identities[] = 'prismicio_tag_' . $document->type . '_' . $tag;
        }

        return $identities;
    }

    /**
     * getDocument() is empty for a "prismicio_by_type_*" root block (never setDocument()'d
     * directly) - go through getContext()'s registry fallback instead, or it'd resolve to nothing.
     */
    private function resolveDocument(): ?\stdClass
    {
        try {
            $document = $this->getDocumentResolver()->getContext('*', $this->getDocument());
        } catch (\Throwable $e) {
            return null;
        }

        return $document instanceof \stdClass ? $document : null;
    }
}
