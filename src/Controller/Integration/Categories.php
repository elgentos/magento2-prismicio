<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Integration;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Categories implements HttpGetActionInterface
{
    /**
     * @var Json
     */
    public $json;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Http $request
     * @param JsonFactory $jsonFactory
     * @param CollectionFactory $productCollectionFactory
     * @param Json $json
     * @param ConfigurationInterface $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Http $request,
        JsonFactory $jsonFactory,
        CollectionFactory $categoryCollectionFactory,
        Json $json,
        ConfigurationInterface $config,
        StoreManagerInterface $storeManager
    )
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
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
        //Protects the route in a way prismic can deal with it.
        $this->protectRoute();

        $attributes = array_unique(
            ['name', 'image', 'description', 'is_active', 'updated_at']
        );

        $categoryCollection = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect($attributes)
            ->addAttributeToSort('updated_at', 'DESC')
            ->setPageSize(50)
            ->setCurPage((int)$this->request->getParam('page', 1));

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $results = array_values(array_map(function ($category) use ($mediaUrl) {
            
            $imageUrl = $category->getImage() ? $mediaUrl . 'catalog/product/' . $category->getImage() : '';

            return [
                'id' => $category->getId(),
                'title' => $category->getName(),
                'description' => $category->getDescription() ?? '',
                'image_url' => $imageUrl,
                'last_update' => (int)date('U', strtotime($category->getUpdatedAt())),
                'blob' => $category->getData()
            ];
        }, $categoryCollection->getItems()));

        $jsonResult = $this->jsonFactory->create();
        $jsonResult->setData([
            'results_size' => $categoryCollection->getSize(),
            'results' => $results
        ]);

        return $jsonResult;
    }

    /**
     *
     */
    private function protectRoute()
    {
        $accessToken = $this->config->getIntegrationFieldsAccessToken($this->storeManager->getStore());

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
