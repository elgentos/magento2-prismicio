<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api\Data\Route;

interface StoreInterface
{
    /**
     * Get the route ID.
     *
     * @return int
     */
    public function getRouteId(): int;

    /**
     * Get the store ID.
     *
     * @return int
     */
    public function getStoreId(): int;
}
