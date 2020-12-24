<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Layout\Metadata;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class Description extends AbstractTemplate
{
    /**
     * Prepare the layout of the template.
     *
     * @return Description
     */
    protected function _prepareLayout(): Description
    {
        if (!$this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig->setMetadata('description', $this->getChildHtml());

        return $this;
    }
}
