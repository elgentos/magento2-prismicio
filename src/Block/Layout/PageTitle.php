<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Layout;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class PageTitle extends AbstractTemplate
{
    /**
     * Prepare the layout of the page title
     *
     * @return $this|PageTitle
     */
    protected function _prepareLayout(): PageTitle
    {
        if (!$this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig
            ->getTitle()
            ->set($this->getChildHtml());

        return $this;
    }
}
