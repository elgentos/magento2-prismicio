<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 13:37
 */

namespace Elgentos\PrismicIO\Controller;


use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{


    public function __construct(

    )
    {
    }

    /**
     * Match application action by request
     *
     * @param RequestInterface $request
     * @return ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $prismicRouteId = 0;

        $request->setModuleName('prismicio')
                ->setControllerName('page')
                ->setActionName('view')
                ->setParam('id', $prismicRouteId);


        return null;

    }

}