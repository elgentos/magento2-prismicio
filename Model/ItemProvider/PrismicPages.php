<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\ItemProvider;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Block\LinkResolverTrait;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Prismic\Predicates;
use Prismic\Dom\Link as PrismicLink;

class PrismicPages implements ItemProviderInterface
{
    use LinkResolverTrait;

    /**
     * @var Api
     */
    protected $apiFactory;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PrismicPageConfigReader
     */
    protected $configReader;

    /**
     * @var array
     */
    protected $sitemapItems = [];

    public function __construct(
        SitemapItemFactory $itemFactory,
        Api $apiFactory,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        LinkResolver $linkResolver,
        PrismicPageConfigReader $configReader
    ) {
        $this->itemFactory = $itemFactory;
        $this->apiFactory = $apiFactory;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->linkResolver = $linkResolver;
        $this->configReader = $configReader;
    }

    public function getItems($storeId): array
    {
        $store = $this->storeManager->getStore($storeId);

        $sitemapContentTypes = $this->getSitemapContentTypes($store);
        $api = $this->apiFactory->create();

        foreach ($sitemapContentTypes as $sitemapContentType) {
            $localeDocuments = $api->query(
                [Predicates::at('document.type', $sitemapContentType)],
                ['lang' => $this->configuration->getContentLanguage($store)]
            );

            foreach (range(1, $localeDocuments->total_pages) as $page) {
                $documents = $api->query(
                    [Predicates::at('document.type', $sitemapContentType)],
                    [
                        'lang' => $this->configuration->getContentLanguage($store),
                        'page' => $page
                    ]
                );

                $this->addDocumentsToSitemap($documents->results, $store);
            }
        }

        return $this->sitemapItems;
    }

    protected function getSitemapContentTypes($store): array
    {
        $sitemapContentTypes = $this->configuration->getSitemapContentTypes($store);

        return array_filter(explode(',', $sitemapContentTypes));
    }

    protected function addDocumentsToSitemap(array $documents, StoreInterface $store)
    {
        if (empty($documents)) {
            return;
        }

        foreach ($documents as $document) {
            $document->store = $store;
            $document->link_type = 'Document';

            $url = $this->getUrl(
                PrismicLink::asUrl($document, $this->getLinkResolver() ?? ''),
                $store
            );

            $this->sitemapItems[] = $this->itemFactory->create(
                [
                    'url' => $url,
                    'updatedAt' => $document->last_publication_date,
                    'priority' => $this->getPriority((int) $store->getId()),
                    'changeFrequency' => $this->getChangeFrequency((int) $store->getId())
                ]
            );
        }
    }

    /**
     * @param int $storeId
     *
     * @return string
     *
     */
    private function getChangeFrequency(int $storeId): string
    {
        return $this->configReader->getChangeFrequency($storeId);
    }

    /**
     * @param int $storeId
     *
     * @return string
     *
     */
    private function getPriority(int $storeId): string
    {
        return $this->configReader->getPriority($storeId);
    }

    public function getUrl(?string $url, StoreInterface $store): string
    {
        return str_replace($store->getBaseUrl(), '', $url);
    }
}
