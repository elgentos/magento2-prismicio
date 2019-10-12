<?php


namespace Elgentos\PrismicIO\Controller\Preview;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;

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

    public function __construct(
        Context $context,
        Api $api,
        LinkResolver $linkResolver,
        RedirectFactory $redirectFactory,
        ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
        $this->api = $api;
        $this->linkResolver = $linkResolver;
        $this->redirectFactory = $redirectFactory;
        $this->forwardFactory = $forwardFactory;
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (! $this->api->isPreviewAllowed()) {
            return $this->forwardFactory->create()
                    ->forward('noroute');
        }

        $token = $this->getRequest()
                ->getParam('token');

        $api = $this->api->create();

        $defaultUrl = '//not-found//';

        $url = $api->previewSession($token, $this->linkResolver, $defaultUrl);
        if ($url === $defaultUrl) {
            return $this->forwardFactory
                ->create()
                ->forward('noroute');
        }

        $redirect = $this->redirectFactory
                ->create();
        $redirect->setUrl($url);

        return $redirect;
    }
}
