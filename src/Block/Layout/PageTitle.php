<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:39
 */

namespace Elgentos\PrismicIO\Block\Layout;

use Elgentos\PrismicIO\Block\AbstractTemplate;

class PageTitle extends AbstractTemplate
{
    protected function _prepareLayout()
    {
        if (! $this->getDocumentResolver()->hasDocument()) {
            return $this;
        }

        $this->pageConfig
                ->getTitle()
                ->set($this->getChildHtml());

        return $this;
    }
}
