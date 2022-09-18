<?php

namespace Elgentos\PrismicIO\Renderer;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Page
{
    /** @var ForwardFactory */
    protected $forwardFactory;
    /** @var RedirectFactory */
    private $redirectFactory;
    /** @var PageFactory */
    private $pageFactory;

    /** @var Api */
    private $api;
    /** @var CurrentDocument */
    private $currentDocument;

    public function __construct(
        ForwardFactory $forwardFactory,
        RedirectFactory $redirectFactory,
        PageFactory $pageFactory,
        Api $api,
        CurrentDocument $currentDocument
    ) {
        $this->forwardFactory = $forwardFactory;
        $this->redirectFactory = $redirectFactory;
        $this->pageFactory = $pageFactory;

        $this->api = $api;
        $this->currentDocument = $currentDocument;
    }

    public function renderPageByUid(string $uid, string $contentType = null): ResultInterface
    {
        if (! $uid) {
            return $this->forwardNoRoute();
        }

        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getDocumentByUid($uid, $contentType);
        if (! $document) {
            return $this->forwardNoRoute();
        }

        if ($document->uid !== $uid) {
            return $this->redirectUid($document->uid);
        }

        return $this->createPage($document);
    }

    public function renderPageBySingleton(string $contentType = null): ResultInterface
    {
        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getSingleton($contentType);
        if (! $document) {
            return $this->forwardNoRoute();
        }

        return $this->createPage($document);
    }

    public function renderPageById(string $id): ResultInterface
    {
        if (! $id) {
            return $this->forwardNoRoute();
        }

        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getDocumentById($id);
        if (! $document) {
            return $this->forwardNoRoute();
        }

        return $this->createPage($document);
    }

    public function forwardNoRoute(): ResultInterface
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('noroute');

        return $resultForward;
    }

    public function redirectUid(string $uid): ResultInterface
    {
        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setPath(
            '*/*/*',
            [
                '_use_rewrite' => false,
                '_current' => true,
                'uid' => $uid
            ]
        );
        $resultRedirect->setHttpResponseCode(301);

        return $resultRedirect;
    }

    public function createPage(\stdClass $document, $pageArguments = []): ResultInterface
    {
        $this->currentDocument->setDocument($document);

        $page = $this->pageFactory->create(false, $pageArguments);
        $page->addHandle([
            'prismicio_default',
            'prismicio_by_type_' . $document->type,
            'prismicio_by_uid_' . $document->uid,
        ]);

        return $page;
    }

}
