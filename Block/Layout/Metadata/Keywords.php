<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Layout\Metadata;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class Keywords extends AbstractTemplate
{
    protected function _prepareLayout(): Keywords|static
    {
        if (!$this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig->setMetadata('keywords', $this->getChildHtml());

        return $this;
    }
}
