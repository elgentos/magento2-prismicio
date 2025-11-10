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

    /** @var LinkResolver */
    private $linkResolver;

    public function __construct(
        Context $context,
        private readonly Api $api,
        LinkResolver $linkResolver,
        private readonly RedirectFactory $redirectFactory,
        private readonly ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
        $this->linkResolver = $linkResolver;
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
