<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
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
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function getCanonicalUrl(): string
    {
        return $this->getCanonical()['url'];
    }

    /**
     * Get canonical url
     *
     * @return array
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function getCanonical(): array
    {
        $link            = $this->getContext();
        $link->link_type = 'Document';
        $href            = $this->getLinkResolver()
            ->resolve($link);

        return [
            'url' => $href,
            'link' => $link
        ];
    }
}
