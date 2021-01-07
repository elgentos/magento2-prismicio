<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Breadcrumbs;

class Wrapper extends Template
{
    /**
     * Add crumbs to the breadcrumbs block.
     *
     * @return Template
     */
    protected function _prepareLayout(): Template
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
     * @param Breadcrumbs $breadcrumbsBlock
     *
     * @return void
     */
    private function addCurrentPageToBreadcrumbs(Breadcrumbs $breadcrumbsBlock): void
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
