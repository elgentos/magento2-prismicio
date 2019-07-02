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
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

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
    /** @var PhpCookieManager */
    private $cookieManager;
    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    public function __construct(
        Context $context,
        Api $api,
        LinkResolver $linkResolver,
        RedirectFactory $redirectFactory,
        ForwardFactory $forwardFactory,
        PhpCookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        parent::__construct($context);
        $this->api = $api;
        $this->linkResolver = $linkResolver;
        $this->redirectFactory = $redirectFactory;
        $this->forwardFactory = $forwardFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
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

        // Set cookie for token
        $metadata = $this->cookieMetadataFactory
                ->createSensitiveCookieMetadata()
                ->setPath('/');

        $this->cookieManager->setSensitiveCookie(
            $api::PREVIEW_COOKIE,
            $token,
            $metadata
        );

        $redirect = $this->redirectFactory
                ->create();
        $redirect->setUrl($url);

        return $redirect;
    }

}
