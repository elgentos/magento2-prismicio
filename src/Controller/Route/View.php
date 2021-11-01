<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Route;

use Elgentos\PrismicIO\Registry\CurrentRoute;
use Elgentos\PrismicIO\Renderer\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultInterface;

class View extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /** @var CurrentRoute */
    private CurrentRoute $currentRoute;

    /** @var Page */
    private Page $page;

    /**
     * Constructor.
     *
     * @param Context      $context
     * @param CurrentRoute $currentRoute
     * @param Page         $page
     */
    public function __construct(
        Context $context,
        CurrentRoute $currentRoute,
        Page $page
    ) {
        parent::__construct($context);

        $this->currentRoute = $currentRoute;
        $this->page         = $page;
    }

    /**
     * View CMS page action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $uid   = $this->getRequest()
            ->getParam('uid');
        $route = $this->currentRoute->getRoute();

        if (!$route) {
            return $this->page->forwardNoRoute();
        }

        return $this->page->renderPageByUid(
            $uid,
            $route->getContentType()
        );
    }
}
