<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Renderer;

use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use stdClass;

class Page
{
    /** @var ForwardFactory */
    protected ForwardFactory $forwardFactory;

    /** @var RedirectFactory */
    private RedirectFactory $redirectFactory;

    /** @var PageFactory */
    private PageFactory $pageFactory;

    /** @var Api */
    private Api $api;

    /** @var CurrentDocument */
    private CurrentDocument $currentDocument;

    public function __construct(
        ForwardFactory $forwardFactory,
        RedirectFactory $redirectFactory,
        PageFactory $pageFactory,
        Api $api,
        CurrentDocument $currentDocument
    ) {
        $this->forwardFactory  = $forwardFactory;
        $this->redirectFactory = $redirectFactory;
        $this->pageFactory     = $pageFactory;
        $this->api             = $api;
        $this->currentDocument = $currentDocument;
    }

    /**
     * Render the page by the given UID
     */
    public function renderPageByUid(
        string $uid,
        ?string $contentType = null
    ): ResultInterface {
        if (!$uid) {
            return $this->forwardNoRoute();
        }

        if (!$this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getDocumentByUid($uid, $contentType);

        if (!$document) {
            return $this->forwardNoRoute();
        }

        if ($document->uid !== $uid) {
            return $this->redirectUid($uid);
        }

        return $this->createPage($document);
    }

    public function renderPageBySingleton(string $contentType = null): ResultInterface
    {
        if (!$this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getSingleton($contentType);

        if (!$document) {
            return $this->forwardNoRoute();
        }

        return $this->createPage($document);
    }

    /**
     * Render the page by the given ID
     */
    public function renderPageById(string $id): ResultInterface
    {
        if (!$id) {
            return $this->forwardNoRoute();
        }

        if (!$this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        $document = $this->api->getDocumentById($id);

        if (!$document) {
            return $this->forwardNoRoute();
        }

        return $this->createPage($document);
    }

    /**
     * Forward the request to the no route page
     */
    public function forwardNoRoute(): ResultInterface
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('noroute');

        return $resultForward;
    }

    /**
     * Redirect the request to the UID page
     */
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

    /**
     * Create he page by the given document and add the required handles
     */
    private function createPage(stdClass $document): ResultInterface
    {
        $this->currentDocument->setDocument($document);

        $page = $this->pageFactory->create();
        $page->addHandle(
            [
                'prismicio_default',
                'prismicio_by_type_' . $document->type,
                'prismicio_by_uid_' . $document->uid,
            ]
        );

        return $page;
    }
}
