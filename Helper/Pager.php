<?php

namespace Elgentos\PrismicIO\Helper;

class Pager extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getPageFromRequest()
    {
        return $this->_getRequest()->getParam('page') ?: 1;
    }
}
