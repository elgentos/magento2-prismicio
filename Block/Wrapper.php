<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Magento\Framework\View\Element\BlockInterface;

class Wrapper extends Template
{
    /**
     * Add crumbs to the breadcrumbs block.
     *
     * @return Template
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbsBlock && $this->getDocumentResolver()->hasDocument()) {
            $this->addCurrentPageToBreadcrumbs($breadcrumbsBlock);
        }

        return parent::_prepareLayout();
    }

    /**
     * Add the crumb for the dynamic page to the breadcrumbs.
     *
     * @param BlockInterface $breadcrumbsBlock
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    private function addCurrentPageToBreadcrumbs(BlockInterface $breadcrumbsBlock): void
    {
        $documentResolver = $this->getDocumentResolver();
        $pageTitle        = $documentResolver->hasContext('data.meta_title')
            ? $documentResolver->getContext('data.meta_title')
            : $documentResolver->getContext('data.title');

        if (is_array($pageTitle)) {
            $pageTitle = $pageTitle[0]->text;
        }

        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->getBaseUrl()
            ]
        );

        if ($pageTitle) {
            $breadcrumbsBlock->addCrumb(
                'page',
                [
                    'label' => $pageTitle,
                    'title' => $pageTitle
                ]
            );
        }
    }
}
