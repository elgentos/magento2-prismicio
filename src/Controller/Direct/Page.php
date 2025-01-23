<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Direct;

use Elgentos\PrismicIO\Renderer\Page as PageRenderer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Elgentos\PrismicIO\Model\Configuration;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Page extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    /** @var PageRenderer */
    public $page;

    /** @var Configuration */
    public $configuration;

    /** @var ResultFactory */
    public $resultFactory;

    /** @var StoreManagerInterface */
    public $storeManager;

    public function __construct(
        Context $context,
        PageRenderer $page,
        StoreManagerInterface $storeManager,
        Configuration $configuration,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->page = $page;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
    }

    /**
     * View CMS page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $id = $this->getRequest()
                ->getParam('id', '');

        if ($id) {
            return $this->page->renderPageById($id);
        }

        $uid = $this->getRequest()
            ->getParam('uid', '');

        $contentType = $this->getRequest()
            ->getParam('type');

        $store = $this->storeManager->getStore();

        if (!$this->configuration->isWhitelistContentTypeWhitelisted($store, $contentType)) {
            return $this->redirectToNotFoundPage();
        }

        return $this->page->renderPageByUid($uid, $contentType);
    }

    public function redirectToNotFoundPage(): ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $result->forward('noroute');
        return $result;
    }
}
