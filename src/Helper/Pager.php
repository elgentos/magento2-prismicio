<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Pager extends AbstractHelper
{
    /**
     * Get the current page from the request
     *
     * @return int
     */
    public function getPageFromRequest(): int
    {
        return (int) $this->_getRequest()->getParam('page') ?: 1;
    }
}
