<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\MultirepoAlternateLinks;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class AlternateLanguage extends AbstractTemplate
{
    public function __construct(
        Template\Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        private readonly StoreManagerInterface $storeManager,
        private readonly ConfigurationInterface $configuration,
        private readonly MultirepoAlternateLinks $multirepoAlternateLinks,
        array $data = []
    ) {
        parent::__construct($context, $documentResolver, $linkResolver, $data);

        $this->setReference('alternate_languages');
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
        $document = $this->getDocumentResolver()->getDocument();
        $context = $this->getContext();

        $mappedContext = [];

        // Add self
        $mappedContext[$document->lang] = $document;

        foreach ($context as $item) {
            $mappedContext[$item->lang] = $item;
        }

        $mappedContext = $this->multirepoAlternateLinks->getAlternateLinks(
            $mappedContext,
            $document
        );

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
        foreach ($this->storeManager->getStores() as $store) {
            if (! $store->getIsActive()) {
                // Skip inactive store
                continue;
            }

            if (! $configuration->getApiEnabled($store)) {
                // Prismic disabled on store
                continue;
            }

            $magentoLanguage = str_replace('_', '-', $store->getConfig('general/locale/code'));

            $language = $configuration->getContentLanguage($store);
            $hasFallback = $configuration->hasContentLanguageFallback($store);
            if ($hasFallback && ! isset($context[$language])) {
                // Overwrite with fallback language
                $language = $configuration->getContentLanguageFallback($store);
            }

            // Use store language
            if ($language === '*') {
                $language = strtolower($magentoLanguage);
            }

            if (! isset($context[$language])) {
                // Not found in language and fallback language
                continue;
            }

            $isDefault = $defaultStoreId === $store->getId();

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
