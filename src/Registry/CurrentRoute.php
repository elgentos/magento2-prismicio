<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 10-4-19
 * Time: 16:01
 */

namespace Elgentos\PrismicIO\Registry;

use Elgentos\PrismicIO\Api\Data\RouteInterface;

class CurrentRoute
{

    /** @var RouteInterface */
    private $route;

    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }
}
