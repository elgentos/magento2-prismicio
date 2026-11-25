<?php


namespace Elgentos\PrismicIO\Controller\Preview;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Store\Model\StoreManagerInterface;

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
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Context $context,
        Api $api,
        LinkResolver $linkResolver,
        RedirectFactory $redirectFactory,
        ForwardFactory $forwardFactory,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->api = $api;
        $this->linkResolver = $linkResolver;
        $this->redirectFactory = $redirectFactory;
        $this->forwardFactory = $forwardFactory;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
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

        // The SDK requests the token as a URL, so only allow the configured Prismic repository
        if (! $this->isValidPreviewToken($token)) {
            return $this->forwardFactory->create()
                    ->forward('noroute');
        }

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

    /**
     * Check that the preview token is an https URL on the host of the configured Prismic repository
     *
     * @param mixed $token
     * @return bool
     */
    private function isValidPreviewToken($token): bool
    {
        if (! is_string($token) || $token === '') {
            return false;
        }

        $tokenParts = parse_url($token);
        if (! is_array($tokenParts)
            || strtolower($tokenParts['scheme'] ?? '') !== 'https'
            || isset($tokenParts['user'])
            || isset($tokenParts['pass'])
            || isset($tokenParts['port'])
        ) {
            return false;
        }

        $endpoint = $this->configuration->getApiEndpoint($this->storeManager->getStore());
        $endpointHost = strtolower((string) parse_url($endpoint, PHP_URL_HOST));
        if ($endpointHost === '') {
            return false;
        }

        $repositoryName = explode('.', $endpointHost)[0];
        $allowedHosts = [
            $endpointHost,
            $repositoryName . '.prismic.io',
            $repositoryName . '.cdn.prismic.io',
        ];

        return in_array(strtolower($tokenParts['host'] ?? ''), $allowedHosts, true);
    }
}
