<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class AlternateLanguage extends AbstractTemplate
{

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        Template\Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        StoreManagerInterface $storeManager,
        ConfigurationInterface $configuration,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
    }

    public function toHtml()
    {
        if ($this->storeManager->isSingleStoreMode()) {
            return '';
        }

        return parent::toHtml();
    }

    public function mapContextWithLanguage()
    {
        $context = $this->getDocumentResolver()
                ->getContext('alternate_languages');

        $mappedContext = [];

        // Add self
        $mappedContext[$this->getContext()->lang] = $this->getContext();

        foreach ($context as $item) {
            $mappedContext[$item->lang] = $item;
        }

        return $mappedContext;
    }

    /**
     * Fetch document view
     *
     * @return array
     */
    public function getAlternateData(): array
    {
        $context = $this->mapContextWithLanguage();
        $configuration = $this->configuration;

        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        $defaultStoreId = $defaultStoreView ? $defaultStoreView->getId() : -1;

        $alternateData = [];
        /** @var StoreInterface $store */
        foreach ($this->storeManager->getStores() as $store) {
            if (! $store->getIsActive()) {
                // Skip inactive store
                continue;
            }

            $language = $configuration->getContentLanguage($store);
            $hasFallback = $configuration->hasContentLanguageFallback($store);
            if ($hasFallback && ! isset($context[$language])) {
                // Overwrite with fallback language
                $language = $configuration->getContentLanguageFallback($store);
            }

            if (! isset($context[$language])) {
                // Not found in language and fallback language
                continue;
            }

            $isDefault = $defaultStoreId === $store->getId();
            $magentoLanguage = str_replace('_', '-', $store->getConfig('general/locale/code'));

            $link = clone $context[$language];

            $link->store = $store;
            $link->link_type = 'Document';
            $href = $this->getLinkResolver()
                    ->resolve($link);

            $alternateData[] = [
                'lang' => $language,
                'store_code' => $store->getCode(),
                'hreflang' => $magentoLanguage,
                'href' => $href,
                'type' => 'text/html',
                'link' => $link
            ];

            if ($isDefault) {
                $alternateData[] = [
                    'lang' => $language,
                    'hreflang' => 'x-default',
                    'href' => $href,
                    'type' => 'text/html',
                    'link' => $link
                ];
            }
        }

        return $alternateData;
    }
}
