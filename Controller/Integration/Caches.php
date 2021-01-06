<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Integration;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Caches implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    public $typeList;
    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    public $pool;
    /**
     * @var ConfigurationInterface
     */
    public $config;

    /**
     * Caches constructor.
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $pool
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\Framework\App\Cache\Frontend\Pool $pool,
        ConfigurationInterface $config
    ) {
        $this->typeList = $typeList;
        $this->pool = $pool;
        $this->config = $config;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->protectRoute()) {
            return;
        }

        // @TODO how can we implement custom tags on Prismic-sourced pages?
        $cleanByDocumentTags = false;
        if ($cleanByDocumentTags) {
            $releases = $this->request('releases');
            $documents = array_merge(
                $this->request('documents'),
                $releases['addition']['documents'] ?? [],
                $releases['update']['documents'] ?? [],
                $releases['deletion']['documents'] ?? []
            );
        }

        $this->cacheTypeList->cleanType(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }

        return;
    }

    /**
     *
     */
    private function protectRoute()
    {
        $accessToken = $this->config->getWebhookSecret($this->storeManager->getStore());

        if ($this->request->getParam('secret') === $accessToken) {
            return true;
        }
    }
}
