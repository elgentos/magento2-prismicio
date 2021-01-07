<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Integration;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Products implements HttpGetActionInterface
{
    /** @var Json */
    public $json;

    /** @var JsonFactory */
    protected $jsonFactory;

    /** @var CollectionFactory */
    protected $productCollectionFactory;

    /** @var Http */
    protected $request;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ConfigurationInterface */
    protected $config;

    /**
     * Constructor
     *
     * @param Http                   $request
     * @param JsonFactory            $jsonFactory
     * @param CollectionFactory      $productCollectionFactory
     * @param Json                   $json
     * @param ConfigurationInterface $config
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        Http $request,
        JsonFactory $jsonFactory,
        CollectionFactory $productCollectionFactory,
        Json $json,
        ConfigurationInterface $config,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonFactory              = $jsonFactory;
        $this->request                  = $request;
        $this->json                     = $json;
        $this->config                   = $config;
        $this->storeManager             = $storeManager;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $this->protectRoute();

        /** @var Store $store */
        $store = $this->storeManager->getStore();

        // Make sure the default attributes needed for Prismic are added
        $attributes = array_unique(
            array_merge(
                ['name', 'image', 'short_description', 'status', 'updated_at'],
                explode(
                    ',',
                    $this->config->getIntegrationFieldsAttributes(
                        $store
                    )
                )
            )
        );

        $visibility = [
            'in' => explode(
                ',',
                $this->config->getIntegrationFieldsVisibility(
                    $store
                )
            )
        ];

        $productCollection = $this->productCollectionFactory->create();

        if (!$this->config->allowSyncDisabledProducts($store)) {
            $productCollection->addAttributeToFilter('status', 1);
        }

        $productCollection->addAttributeToFilter('visibility', $visibility)
            ->addAttributeToSelect($attributes)
            ->addAttributeToSort('updated_at', 'DESC');

        $productCollection->setPageSize(50);
        $productCollection->setCurPage((int)$this->request->getParam('page', 1));

        $mediaUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $results  = array_values(
            array_map(
                function ($product) use ($mediaUrl) {
                    return [
                        'id' => $product->getId(),
                        'title' => $product->getName(),
                        'description' => $product->getShortDescription() ?? '',
                        'image_url' => $mediaUrl . 'catalog/product/' . $product->getImage(),
                        'last_update' => (int)date('U', strtotime($product->getUpdatedAt())),
                        'blob' => $product->getData()
                    ];
                },
                $productCollection->getItems()
            )
        );

        $jsonResult = $this->jsonFactory->create();
        $jsonResult->setData(
            [
            'results_size' => $productCollection->getSize(),
            'results' => $results
            ]
        );

        return $jsonResult;
    }

    /**
     * @return null
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    private function protectRoute()
    {
        $accessToken = $this->config->getIntegrationFieldsAccessToken(
            $this->storeManager->getStore()
        );

        if (!$accessToken) {
            return null;
        }

        if (empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $accessToken) {
            header('WWW-Authenticate: Basic realm="Prismic Integration"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authentication necessary - see Access Token in Prismic under ' .
                'Settings > Integration Fields > your custom integration';
            exit;
        }

        return null;
    }
}
