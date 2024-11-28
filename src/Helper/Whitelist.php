<?php

namespace Elgentos\PrismicIO\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Whitelist
{

    private const CONFIG_PATH_WHITELIST_ENABLED = 'prismicio/whitelist/enabled';

    private const CONFIG_PATH_WHITELIST_CONTENT_TYPES = 'prismicio/whitelist/content_types';

    protected $contentTypes = [];

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
        $this->contentTypes = explode(',', $this->scopeConfig->getValue(
            self::CONFIG_PATH_WHITELIST_CONTENT_TYPES,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->storeManager->getStore()->getId()
        ));
    }

    private function isWhitelistEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_WHITELIST_ENABLED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->storeManager->getStore()->getId()
        );
    }

    public function isWhitelistContentTypeWhitelisted(string $contentType): bool
    {
        if (!$this->isWhitelistEnabled()) {
            return true;
        }

        return in_array($contentType, $this->contentTypes);
    }
}
