<?php

namespace Elgentos\PrismicIO\Block;

class Canonical extends AbstractTemplate
{
    public function getCanonicalUrl(): string
    {
        return $this->getCanonical()['url'];
    }

    public function getCanonical(): array
    {
        $link = clone $this->getContext();
        $link->link_type = 'Document';

        $href = $this->getLinkResolver()
            ->resolve($link);

        return [
            'url' => $href,
            'link' => $link
        ];
    }
}
