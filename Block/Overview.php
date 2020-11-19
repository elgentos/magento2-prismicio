<?php


namespace Elgentos\PrismicIO\Block;


use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\View\Element\Template;
use Prismic\Predicates;
use Prismic\SimplePredicate;

class Overview extends Template
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

        $localeDocuments = $api->query(
            $query,
            $this->apiFactory->getOptions($this->options)
        );

        $fallbackDocuments = new \stdClass;
        if ($this->configuration->hasContentLanguageFallback($this->_storeManager->getStore())) {
            $fallbackDocuments = $api->query(
                $query,
                $this->apiFactory->getOptionsLanguageFallback($this->options)
            );
        }

        return $this->mergeDocuments($localeDocuments, $fallbackDocuments);
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
     * Merge documents from diverent results and deduplicate keeping the first
     *
     * @param \stdClass ...$allDocuments
     * @return array
     */
    public function mergeDocuments(\stdClass ...$allDocuments): array
    {
        $results = [];
        foreach ($allDocuments as $documents) {
            $foundDocuments = $documents->results ?? [];
            if ( empty($foundDocuments)) {
                continue;
            }

            $results = array_merge($results, $foundDocuments);
        }

        // Deduplicate
        $ids = [];
        foreach ($results as $index => $document) {
            $id = $document->id;
            $alternateLangIds = array_filter(array_map(function($langDocument) {
                return $langDocument->id ?? null;
            }, $document->alternate_languages ?? []));

            if (isset($ids[$id])) {
                unset($results[$index]);
                continue;
            }

            $ids[$id] = true;
            $ids = array_merge($ids, array_fill_keys($alternateLangIds, true));
        }

        return array_values($results);
    }
}
