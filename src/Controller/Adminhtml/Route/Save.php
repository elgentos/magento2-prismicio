<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Adminhtml\Route;

use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\Collection as RouteStoreCollection;
use Elgentos\PrismicIO\Model\ResourceModel\Route as RouteResource;
use Elgentos\PrismicIO\Model\ResourceModel\Route\Store as RouteStoreResource;
use Elgentos\PrismicIO\Model\RouteFactory;
use Magento\Framework\Exception\LocalizedException;
use Elgentos\PrismicIO\Model\Route\StoreFactory as RouteStoreFactory;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var RouteStoreFactory
     */
    public $routeStoryFactory;
    /**
     * @var RouteStoreCollection
     */
    public $routeStoreCollection;
    protected $dataPersistor;

    private RouteResource $routeResource;

    private RouteStoreResource $routeStoreResource;

    private RouteFactory $routeFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param RouteStoreCollection $routeStoreCollection
     * @param RouteStoreFactory $routeStoreFactory
     * @param RouteResource $routeResource
     * @param RouteStoreResource $routeStoreResource
     * @param RouteFactory $routeFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        RouteStoreCollection $routeStoreCollection,
        RouteStoreFactory $routeStoreFactory,
        RouteResource $routeResource,
        RouteStoreResource $routeStoreResource,
        RouteFactory $routeFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->routeStoryFactory = $routeStoreFactory;
        parent::__construct($context);
        $this->routeStoreCollection = $routeStoreCollection;
        $this->routeResource = $routeResource;
        $this->routeStoreResource = $routeStoreResource;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('route_id');

            $model = $this->routeFactory->create();
            $this->routeResource->load($model, $id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Route no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $this->routeResource->save($model);

                $this->routeStoreCollection->addFieldToFilter('route_id', $model->getId())->each(function ($routeStore) {
                    $this->routeStoreResource->delete($routeStore);
                });
                foreach ($model->getData('store_id') as $storeId) {
                    $routeStore = $this->routeStoryFactory->create()->setData([
                        'route_id' => $model->getId(),
                        'store_id' => $storeId
                    ]);
                    $this->routeStoreResource->save($routeStore);
                }

                $this->messageManager->addSuccessMessage(__('You saved the Route.'));
                $this->dataPersistor->clear('prismicio_route');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['route_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Route.'));
            }

            $this->dataPersistor->set('prismicio_route', $data);
            return $resultRedirect->setPath('*/*/edit', ['route_id' => $this->getRequest()->getParam('route_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
