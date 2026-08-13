<?php
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

/**
 * Serves prismicio/slice/slicesimulator at "/slice-simulator" (or "/secret/{secret}/slice-simulator"),
 * matching the URL convention Prismic's own tooling expects.
 */
class SliceSimulatorRouter implements RouterInterface
{
    public function __construct(
        protected ActionFactory $actionFactory
    ) {
    }

    public function match(RequestInterface $request): ?\Magento\Framework\App\ActionInterface
    {
        if (!$request instanceof HttpRequest) {
            return null;
        }

        // A forward() from the target action (e.g. to "noroute" on an invalid secret) re-dispatches through
        // every router again with the same path info - without this guard we'd match and forward right back,
        // looping until Magento's front controller hits its iteration limit.
        if ($request->getParam('prismicio_slice_simulator_matched')) {
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');

        if (!preg_match('#^(?:secret/([^/]+)/)?slice-simulator$#', $identifier, $matches)) {
            return null;
        }

        $request->setParam('prismicio_slice_simulator_matched', true);

        if (isset($matches[1])) {
            $request->setParam('secret', $matches[1]);
        }

        $request->setModuleName('prismicio')
            ->setControllerName('slice')
            ->setActionName('slicesimulator');

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }
}
