<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Block\Layout\Metadata;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class Robots extends AbstractTemplate
{
    protected function _prepareLayout()
    {
        if (! $this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig->setMetadata('robots', $this->getChildHtml());
        return $this;
    }
}
