<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api\Data\Route;

interface StoreInterface
{
    public function getRouteId(): int;
    public function getStoreId(): int;
}
