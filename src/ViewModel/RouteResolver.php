<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Registry\CurrentRoute;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class RouteResolver implements ArgumentInterface
{
    /** @var CurrentRoute */
    private CurrentRoute $currentRoute;

    /**
     * Constructor.
     *
     * @param CurrentRoute $currentRoute
     */
    public function __construct(
        CurrentRoute $currentRoute
    ) {
        $this->currentRoute = $currentRoute;
    }

    /**
     * Get the route from the current route
     *
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        return $this->currentRoute->getRoute();
    }

    /**
     * Check if the current route has an actual route
     *
     * @return bool
     */
    public function hasRoute(): bool
    {
        return (bool)$this->currentRoute->getRoute();
    }
}
