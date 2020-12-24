<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\LinkResolver;

trait LinkResolverTrait
{
    /** @var LinkResolver */
    private $linkResolver;

    /**
     * Get the link resolver
     *
     * @return LinkResolver
     */
    public function getLinkResolver(): LinkResolver
    {
        return $this->linkResolver;
    }
}
