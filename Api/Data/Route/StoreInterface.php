<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 10-4-19
 * Time: 17:17
 */

namespace Elgentos\PrismicIO\Api\Data\Route;

interface StoreInterface
{
    public function getRouteId(): int;
    public function getStoreId(): int;
}
