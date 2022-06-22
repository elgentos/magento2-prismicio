<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

class Url implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;
    private ConfigurationInterface $configuration;
    private StoreManagerInterface $storeManager;

    public function __construct(
        RequestInterface $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $params = $this->request->getParams();

        $urlRewriteDocumentTypes = $this->configuration->getUrlRewriteContentTypes(
            $this->storeManager->getStore()
        );

        if (!$urlRewriteDocumentTypes) {
            // return
            return;
        }


        var_dump($urlRewriteDocumentTypes);die;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
