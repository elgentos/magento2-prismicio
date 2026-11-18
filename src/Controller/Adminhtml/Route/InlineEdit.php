<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Adminhtml\Route;

use Elgentos\PrismicIO\Model\ResourceModel\Route as RouteResource;
use Elgentos\PrismicIO\Model\RouteFactory;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;

    private RouteResource $routeResource;

    private RouteFactory $routeFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param RouteResource $routeResource
     * @param RouteFactory $routeFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        RouteResource $routeResource,
        RouteFactory $routeFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->routeResource = $routeResource;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    /** @var \Elgentos\PrismicIO\Model\Route $model */
                    $model = $this->routeFactory->create();
                    $this->routeResource->load($model, $modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $this->routeResource->save($model);
                    } catch (\Exception $e) {
                        $messages[] = "[Route ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
