<?php


namespace Elgentos\PrismicIO\Block;


use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Prismic\Predicates;
use Prismic\SimplePredicate;

class Overview extends AbstractCachedBlock
{

    /**
     * @var array
     */
    private $filters = [];
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var string
     */
    private $documentType;
    /**
     * @var Api
     */
    private $apiFactory;
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * Overview constructor.
     * @param Template\Context $context
     * @param Api $apiFactory
     * @param ConfigurationInterface $configuration
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Api $apiFactory,
        ConfigurationInterface $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->apiFactory = $apiFactory;
        $this->configuration = $configuration;
    }

    /**
     * Set allowed filters from the get params
     *
     * @param array $filters
     */
    public function setAllowedFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * Set additional options to supply on request
     *
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Set the document type to fetch
     *
     * @param string $documentType
     */
    public function setDocumentType(string $documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * Get documents for locale and fallback
     *
     * @return array
     * @throws \Elgentos\PrismicIO\Exception\ApiNotEnabledException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDocuments(): array
    {
        if (! $this->documentType) {
            return [];
        }

        $api = $this->apiFactory->create();
        $query = $this->buildQuery();

        // Single API call that includes fallback language if enabled
        $documents = $api->query(
            $query,
            $this->apiFactory->getOptions($this->options, true)
        );

        return isset($documents->results) ? $documents->results : [];
    }

    /**
     * Get documents without language, will render links in local store
     *
     * @return array
     * @throws \Elgentos\PrismicIO\Exception\ApiNotEnabledException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDocumentsWithoutLanguage(): array
    {
        return array_map(function($document) {
            unset($document->lang);
            return $document;
        }, $this->getDocuments());
    }

    /**
     *
     * @return SimplePredicate[]
     */
    public function buildFilters(): array
    {
        $filters = [];
        $params = $this->getRequest()->getParams();
        foreach ($this->filters as $param => $filter) {
            if (! isset($params[$param])) {
                continue;
            }

            $filters[$param] = Predicates::at($filter, rtrim($params[$param], '/'));
        }

        return $filters;
    }

    /**
     *
     * @return SimplePredicate[]
     */
    public function buildQuery()
    {
        $filters = $this->buildFilters();
        $filters[] = Predicates::at('document.type', $this->documentType);

        return $filters;
    }

    /**
     * Get cache key info for this block
     *
     * @return array
     */
    protected function getCacheKeyInfo(): array
    {
        try {
            $store = $this->_storeManager->getStore();
            return [
                'prismic_overview',
                $this->documentType ?? 'default',
                $store->getId(),
                md5(json_encode($this->filters) . json_encode($this->options))
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get cache lifetime in seconds
     *
     * @return int|null
     */
    protected function getCacheLifetime(): ?int
    {
        // Disable caching in preview mode
        if ($this->isPreviewMode()) {
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
        return ['PRISMICIO_OVERVIEW', 'PRISMICIO_DOC_' . ($this->documentType ?? 'default')];
    }
}
