<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

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
     * Get canonical url
     *
     * @return array
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
