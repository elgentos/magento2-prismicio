<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Direct;

use Elgentos\PrismicIO\Renderer\Page as PageRenderer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Elgentos\PrismicIO\Helper\Whitelist;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Page extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    /** @var PageRenderer */
    public $page;

    /** @var Whitelist */
    public $whitelist;

    /** @var ResultFactory */
    public $resultFactory;

    public function __construct(
        Context $context,
        PageRenderer $page,
        Whitelist $whitelist,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->page = $page;
        $this->whitelist = $whitelist;
        $this->resultFactory = $resultFactory;
    }

    /**
     * View CMS page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
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

        if (!$this->whitelist->isWhitelistContentTypeWhitelisted($contentType)) {
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
