<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Direct;

use Elgentos\PrismicIO\Renderer\Page as PageRenderer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;

class Page extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    /** @var PageRenderer */
    public $page;

    public function __construct(
        Context $context,
        PageRenderer $page
    ) {
        parent::__construct($context);
        $this->page = $page;
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

        return $this->page->renderPageByUid($uid, $contentType);
    }
}
