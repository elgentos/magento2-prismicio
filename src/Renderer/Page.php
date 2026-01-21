<?php

namespace Elgentos\PrismicIO\Renderer;

use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Model\Document\CacheManager;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use stdClass;

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
    /** @var CacheManager */
    private $cacheManager;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        ForwardFactory $forwardFactory,
        RedirectFactory $redirectFactory,
        PageFactory $pageFactory,
        Api $api,
        CurrentDocument $currentDocument,
        CacheManager $cacheManager,
        StoreManagerInterface $storeManager
    ) {
        $this->forwardFactory = $forwardFactory;
        $this->redirectFactory = $redirectFactory;
        $this->pageFactory = $pageFactory;

        $this->api = $api;
        $this->currentDocument = $currentDocument;
        $this->cacheManager = $cacheManager;
        $this->storeManager = $storeManager;
    }
            // For singletons, UID should match the type (as per original design: $uid = $type)
            // Use $uid if available, otherwise fallback to $cacheType

    /**
     * @throws NoSuchEntityException
     * @throws ApiNotEnabledException
     */
    public function renderPageByUid(string $uid, ?string $contentType = null): ResultInterface
    {
        if (! $uid) {
            return $this->forwardNoRoute();
        }

        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        // Get language from API options
        $options = $this->api->getOptions();
        $lang = $options['lang'];
        $type = $contentType;

        // Get store and website info
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        $websiteId = (int)$store->getWebsiteId();

        // Try to get document from cache
        $document = $this->cacheManager->get($type, $uid, $lang, $storeId, $websiteId);

        // If not cached, fetch from API and cache it
        if ($document === null) {
            $document = $this->api->getDocumentByUid($uid, $contentType);

            if (! $document) {
                return $this->forwardNoRoute();
            }

            // Cache the document for next request
            $this->cacheManager->set($document, $type, $uid, $lang, $storeId, $websiteId);
        }

        if (! $document) {
            return $this->forwardNoRoute();
        }

        if ($document->uid !== $uid) {
            return $this->redirectUid($document->uid);
        }

        return $this->createPage($document);
    }

    /**
     * @throws NoSuchEntityException
     * @throws ApiNotEnabledException
     */
    public function renderPageBySingleton(?string $contentType = null): ResultInterface
    {
        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        // Get language from API options
        $options = $this->api->getOptions();
        $lang = $options['lang'];
        $type = $contentType;

        // Use content type as UID for singleton cache key
        $uid = $type;

        // Get store and website info
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        $websiteId = (int)$store->getWebsiteId();

        // Try to get document from cache
        $document = $this->cacheManager->get($type, $uid, $lang, $storeId, $websiteId);

        // If not cached, fetch from API and cache it
        if ($document === null) {
            $document = $this->api->getSingleton($contentType);

            if (! $document) {
                return $this->forwardNoRoute();
            }

            // Cache the document for next request
            $this->cacheManager->set($document, $type, $uid, $lang, $storeId, $websiteId);
        }

        if (! $document) {
            return $this->forwardNoRoute();
        }

        return $this->createPage($document);
    }

    /**
     * @throws NoSuchEntityException
     * @throws ApiNotEnabledException
     */
    public function renderPageById(string $id): ResultInterface
    {
        if (! $id) {
            return $this->forwardNoRoute();
        }

        if (! $this->api->isActive()) {
            return $this->forwardNoRoute();
        }

        // Get language from API options
        $options = $this->api->getOptions();
        $lang = $options['lang'];

        // Get store and website info
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        $websiteId = (int)$store->getWebsiteId();

        // Try to get document from cache using ID as uid
        $document = $this->cacheManager->get('by_id', $id, $lang, $storeId, $websiteId);

        // If not cached, fetch from API and cache it
        if ($document === null) {
            $document = $this->api->getDocumentById($id);

            if (! $document) {
                return $this->forwardNoRoute();
            }

            // Cache the document for next request
            $this->cacheManager->set($document, 'by_id', $id, $lang, $storeId, $websiteId);
        }

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

    /**
     * @param stdClass $document
     * @param array    $pageArguments
     *
     * @return ResultInterface
     */
    public function createPage(stdClass $document, array $pageArguments = []): ResultInterface
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
