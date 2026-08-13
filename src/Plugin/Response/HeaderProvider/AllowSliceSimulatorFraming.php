<?php
declare(strict_types=1);

namespace Elgentos\PrismicIO\Plugin\Response\HeaderProvider;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\HeaderProvider\XFrameOptions;

/**
 * X-Frame-Options is set independently of the frame-ancestors CSP policy in SliceSimulator::modifyCsp()
 * and isn't overridden by it under a report-only CSP, so it must be suppressed here directly.
 */
class AllowSliceSimulatorFraming
{
    private const FULL_ACTION_NAME = 'prismicio_slice_slicesimulator';

    public function __construct(
        private readonly HttpRequest $request
    ) {
    }

    public function afterCanApply(XFrameOptions $subject, bool $result): bool
    {
        return $result && $this->request->getFullActionName() !== self::FULL_ACTION_NAME;
    }
}
