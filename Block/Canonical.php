<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends AbstractTemplate
{
    public function getCanonicalUrl(): string
    {
        return $this->getCanonical()['url'];
    }

    public function getCanonical(): array
    {
        $link = $this->getContext();

        $link->link_type = 'Document';
        $href = $this->getLinkResolver()
            ->resolve($link);

        return [
            'url' => $href,
            'link' => $link
        ];
    }
}
