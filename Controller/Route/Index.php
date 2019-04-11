<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 8:52
 */

namespace Elgentos\PrismicIO\Controller\Route;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    private $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
    }


    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        return $page;
    }

}