<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Layout\Metadata;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class Title extends AbstractTemplate
{
    /**
     * Prepare the layout of the title
     *
     * @return Title
     */
    protected function _prepareLayout(): Title
    {
        if (!$this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig->setMetadata('title', $this->getChildHtml());

        return $this;
    }
}
