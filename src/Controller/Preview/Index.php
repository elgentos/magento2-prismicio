<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Preview;

use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Index extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /** @var Api */
    private $api;

    /** @var LinkResolver */
    private $linkResolver;

    /** @var RedirectFactory */
    private $redirectFactory;

    /** @var ForwardFactory */
    private $forwardFactory;

    /**
     * Constructor.
     *
     * @param Context         $context
     * @param Api             $api
     * @param LinkResolver    $linkResolver
     * @param RedirectFactory $redirectFactory
     * @param ForwardFactory  $forwardFactory
     */
    public function __construct(
        Context $context,
        Api $api,
        LinkResolver $linkResolver,
        RedirectFactory $redirectFactory,
        ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
        $this->api             = $api;
        $this->linkResolver    = $linkResolver;
        $this->redirectFactory = $redirectFactory;
        $this->forwardFactory  = $forwardFactory;
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws ApiNotEnabledException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if (! $this->api->isPreviewAllowed()) {
            return $this->forwardFactory->create()
                ->forward('noroute');
        }

        $token      = $this->getRequest()
            ->getParam('token');
        $api        = $this->api->create();
        $defaultUrl = '//not-found//';
        $url        = $api->previewSession(
            $token,
            $this->linkResolver,
            $defaultUrl
        );

        if ($url === $defaultUrl) {
            return $this->forwardFactory->create()
                ->forward('noroute');
        }

        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($url);

        return $redirect;
    }
}
