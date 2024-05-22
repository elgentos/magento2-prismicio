<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api;

use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Exception\RouteNotFoundException;

interface RouteRepositoryInterface
{
    /**
     * Get Route by id
     *
     * @param string $contentType
     * @param int $storeId
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function get(int $id): RouteInterface;

    /**
     * Get Route by content type
     *
     * @param string $contentType
     * @param int $storeId
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function getByContentType(string $contentType, int $storeId): RouteInterface;
}
