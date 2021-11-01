<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Registry;

use Elgentos\PrismicIO\Api\Data\RouteInterface;

class CurrentRoute
{
    /** @var RouteInterface */
    private RouteInterface $route;

    /**
     * Set the current route
     *
     * @param RouteInterface $route
     *
     * @return void
     */
    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

    /**
     * Get the current route
     *
     * @return RouteInterface|null
     */
    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }
}
