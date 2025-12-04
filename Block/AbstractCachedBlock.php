<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

abstract class AbstractCachedBlock extends Template implements IdentityInterface
{
    /**
     * Get cache key info for this block
     *
     * @return array
     */
    abstract protected function getCacheKeyInfo(): array;

    /**
     * Get cache lifetime in seconds
     * Return null to disable caching or in preview mode
     *
     * @return int|null
     */
    protected function getCacheLifetime(): ?int
    {
        // Disable caching in preview mode
        if ($this->isPreviewMode()) {
            return null;
        }

        // Default cache lifetime: 1 hour
        return 3600;
    }

    /**
     * Check if we're in preview mode
     *
     * @return bool
     */
    protected function isPreviewMode(): bool
    {
        // Check for Prismic preview token in request
        $request = $this->getRequest();
        if ($request && $request->getParam('token')) {
            return true;
        }

        return false;
    }

    /**
     * Get cache tags for this block
     * Used for selective cache invalidation
     *
     * @return array
     */
    public function getCacheTags(): array
    {
        return array_merge(['PRISMICIO_API'], $this->getIdentities());
    }

    /**
     * Get identities for cache invalidation
     * Should be overridden by subclasses to provide document-specific tags
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [];
    }

    /**
     * Get block cache key
     * Used by Magento's caching system
     *
     * @return string|null
     */
    public function getCacheKey(): ?string
    {
        if ($this->getCacheLifetime() === null) {
            return null;
        }

        $keyInfo = $this->getCacheKeyInfo();
        if (empty($keyInfo)) {
            return null;
        }

        return $this->getCacheKeyInfoString($keyInfo);
    }

    /**
     * Convert cache key info array to string
     *
     * @param array $keyInfo
     * @return string
     */
    private function getCacheKeyInfoString(array $keyInfo): string
    {
        $keys = [];
        foreach ($keyInfo as $key => $value) {
            if (is_array($value)) {
                $value = implode('_', $value);
            }
            $keys[] = $key . '_' . $value;
        }

        return implode('_', $keys);
    }
}
