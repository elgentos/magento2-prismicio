<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 13:37
 */

namespace Elgentos\PrismicIO\Controller;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Collection;
use Elgentos\PrismicIO\Model\ResourceModel\Route\CollectionFactory;
use Elgentos\PrismicIO\Registry\CurrentRoute;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Router implements RouterInterface
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;
    /**
     * @var CurrentRoute
     */
    private $currentRoute;
    /**
     * @var Collection
     */
    private $collection;
    /**
     * @var ActionFactory
     */
    private $actionFactory;
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        StoreManagerInterface $storeManager,
        CollectionFactory $collection,
        SearchCriteriaInterface $searchCriteria,
        CurrentRoute $currentRoute,
        ActionFactory $actionFactory,
        ConfigurationInterface $configuration
    ) {
        $this->storeManager = $storeManager;
        $this->collection = $collection;
        $this->searchCriteria = $searchCriteria;
        $this->currentRoute = $currentRoute;
        $this->actionFactory = $actionFactory;
        $this->configuration = $configuration;
    }

    /**
     * Match application action by request
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     * @throws NoSuchEntityException
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        $store = $this->storeManager->getStore();
        if (! $this->configuration->getApiEnabled($store)) {
            return null;
        }

        if ($this->currentRoute->getRoute()) {
            // Already initialized
            return null;
        }

        $requestPath = $request->getPathInfo();

        /** @var Collection $collection */
        $collection = $this->collection->create();

        $collection->filterByStoreId(+$store->getId());
        $collection->filterByStatus(true);
        $collection->filterByRequestPath($requestPath);

        $collection->setPageSize(1);
        if ($collection->getSize() < 1) {
            return null;
        }

        /** @var RouteInterface $route */
        $route = $collection->getFirstItem();
        $this->currentRoute->setRoute($route);

        $prismicRouteUid = $route->getUidForRequestPath($requestPath);

        $request->setModuleName('prismicio')
            ->setControllerName('route')
            ->setActionName('view')
            ->setParam('uid', $prismicRouteUid);

        if (! $prismicRouteUid) {
            $request->setModuleName('prismicio')
                ->setControllerName('route')
                ->setActionName('index');
        }

        return $this->actionFactory->create(Forward::class);
    }
}
