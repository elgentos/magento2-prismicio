<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Adminhtml\Route;

use Elgentos\PrismicIO\Model\ResourceModel\Route\Store\Collection as RouteStoreCollection;
use Elgentos\PrismicIO\Model\Route\Store;
use Magento\Framework\Exception\LocalizedException;
use Elgentos\PrismicIO\Model\Route\StoreFactory as RouteStoreFactory;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var RouteStoreFactory
     */
    public $routeStoryFactory;
    /**
     * @var Collection
     */
    public $routeStoreCollection;
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        RouteStoreCollection $routeStoreCollection,
        RouteStoreFactory $routeStoreFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->routeStoryFactory = $routeStoreFactory;
        parent::__construct($context);
        $this->routeStoreCollection = $routeStoreCollection;
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

            $model = $this->_objectManager->create(\Elgentos\PrismicIO\Model\Route::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Route no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();

                $this->routeStoreCollection->addFieldToFilter('route_id', $model->getId())->each(function ($routeStore) {
                    $routeStore->delete();
                });
                foreach ($model->getData('store_id') as $storeId) {
                    $this->routeStoryFactory->create()->setData([
                        'route_id' => $model->getId(),
                        'store_id' => $storeId
                    ])->save();
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

