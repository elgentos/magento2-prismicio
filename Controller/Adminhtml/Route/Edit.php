<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Adminhtml\Route;

class Edit extends \Elgentos\PrismicIO\Controller\Adminhtml\Route
{

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    public $dataPersistor;
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('route_id');
        $model = $this->_objectManager->create(\Elgentos\PrismicIO\Model\Route::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Route no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $model->setData('store_id', implode(',', $model->getStoreIds()));
        $this->dataPersistor->set('prismicio_route', $model->getData());

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Route') : __('New Route'),
            $id ? __('Edit Route') : __('New Route')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Routes'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Route %1', $model->getTitle()) : __('New Route'));
        return $resultPage;
    }
}

