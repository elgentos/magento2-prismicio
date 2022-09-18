<?php


namespace Elgentos\PrismicIO\ViewModel;


use Elgentos\PrismicIO\Api\Data\RouteInterface;
use Elgentos\PrismicIO\Registry\CurrentRoute;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class RouteResolver implements ArgumentInterface
{

    /**
     * @var CurrentRoute
     */
    private $currentRoute;

    public function __construct(
        CurrentRoute $currentRoute
    ) {
        $this->currentRoute = $currentRoute;
    }

    public function getRoute(): RouteInterface
    {
        return $this->currentRoute->getRoute();
    }

    public function hasRoute(): bool
    {
        return (bool)$this->currentRoute->getRoute();
    }

}
