<?php

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends AbstractTemplate
{
    /**
     * Get canonical url
     *
     * @return string
     */
    public function getCanonicalUrl(): string
    {
        return $this->getCanonical()['url'];
    }

    /**
     * Get canical url
     *
     * @return array
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
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
