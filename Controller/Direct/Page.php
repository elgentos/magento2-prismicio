<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 10-4-19
 * Time: 17:41
 */

namespace Elgentos\PrismicIO\Controller\Direct;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Elgentos\PrismicIO\Registry\CurrentRoute;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Prismic\Predicates;


class Page extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var Api
     */
    private $api;

    /**
     * @var CurrentDocument
     */
    private $currentDocument;
    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        Api $api,
        CurrentDocument $currentDocument,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->resultForwardFactory = $resultForwardFactory;
        $this->api = $api;
        $this->currentDocument = $currentDocument;
        $this->pageFactory = $pageFactory;
    }

    /**
     * View CMS page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $uid = $this->getRequest()
                ->getParam('uid');
        if (! $uid) {
            return $this->forwardIndex();
        }

        $api = $this->api;

        $contentType = $api->getDefaultContentType();
        if (! $contentType) {
            return $this->forwardNoRoute();
        }

        $document = $api->create()
                ->getByUID($contentType, $uid, $api->getOptions());
        if (! $document) {
            return $this->forwardNoRoute();
        }

        $this->currentDocument->setDocument($document);

        return $this->renderPage();
    }

    /**
     * @return ResultInterface
     */
    public function forwardNoRoute(): ResultInterface
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('noroute');

        return $resultForward;
    }

    /**
     * @return ResultInterface
     */
    public function forwardIndex(): ResultInterface
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('index');

        return $resultForward->forward('index');
    }

    public function redirectUid(string $uid): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $redirectUrl = trim($this->currentRoute->getRoute()->getRoute(), '/') . '/' . $uid;

        $resultRedirect->setPath('',
             [
                 '_use_rewrite' => false,
                 '_direct' => $redirectUrl
             ]
        );
        $resultRedirect->setHttpResponseCode(301);

        return $resultRedirect;
    }

    public function renderPage(): ResultInterface
    {
        $page = $this->pageFactory->create();
        $api = $this->api;
        $document = $this->currentDocument->getDocument();

        $page->addHandle([
            'prismicio_default',
            'prismicio_direct_page',
            'prismicio_direct_page_' . $api->getDefaultContentType(),
            'prismicio_direct_page_' . $document->uid,
        ]);

        return $page;
    }

}