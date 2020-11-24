<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Integration;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductCollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;

class Products implements HttpGetActionInterface
{
    /**
     * @var Json
     */
    public $json;
    /**
     * @var ScopeConfigInterface
     */
    public $config;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Http
     */
    protected $request;

    /**
     * Constructor
     *
     * @param Http $request
     * @param JsonFactory $jsonFactory
     * @param CollectionFactory $productCollectionFactory
     * @param Json $json
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        Http $request,
        JsonFactory $jsonFactory,
        CollectionFactory $productCollectionFactory,
        Json $json,
        ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->json = $json;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->protectRoute();
        // Make sure the default attributes needed for Prismic are added
        $attributes = array_unique(
            array_merge(
                ['name', 'image', 'short_description', 'status', 'updated_at'],
                explode(',', $this->config->getValue('prismicio/integration_fields/attributes'))
            )
        );
        $synchronizeDisabledProducts = $this->config->getValue('prismicio/integration_fields/sync_disabled_products');
        $visibility = ['in' => explode(',', $this->config->getValue('prismicio/integration_fields/visibility'))];
        $productCollection = $this->productCollectionFactory
            ->create();
        if (!$synchronizeDisabledProducts) {
            $productCollection->addAttributeToFilter('status', 1);
        }
        $productCollection->addAttributeToFilter('visibility', $visibility)
            ->addAttributeToSelect($attributes)
            ->addAttributeToSort('updated_at', 'DESC');

        $productCollection->setPageSize(50);
        $productCollection->setCurPage($this->request->getParam('page'));

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $results = array_values(array_map(function ($product) use ($mediaUrl) {
            return [
                'id' => $product->getId(),
                'title' => $product->getName(),
                'description' => $product->getShortDescription() ?? '',
                'image_url' => $mediaUrl . 'catalog/product/' . $product->getImage(),
                'last_update' => intval(date('U', strtotime($product->getUpdatedAt()))),
                'blob' => $product->getData()
            ];
        }, $productCollection->getItems()));

        $jsonResult = $this->jsonFactory->create();
        $jsonResult->setData([
            'results_size' => $productCollection->getSize(),
            'results' => $results
        ]);

        return $jsonResult;
    }

    /**
     *
     */
    private function protectRoute()
    {
        $accessToken = $this->config->getValue('prismicio/integration_fields/access_token');

        if (!$accessToken) {
            return null;
        }

        if (empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $accessToken) {
            header('WWW-Authenticate: Basic realm="Prismic Integration"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authentication necessary - see Access Token in Prismic under Settings > Integration Fields > your custom integration';
            exit;
        }
    }
}

