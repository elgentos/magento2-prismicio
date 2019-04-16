<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block\Dom;

use Magento\Framework\App\ObjectManager\Environment\Developer;

class Debug extends AbstractDom
{

    public function fetchDocumentView(): string
    {
        if ($this->_appState->getMode() !== Developer::MODE) {
            // Only allow debug in developer mode
            return '';
        }

        return '<pre>' . print_r($this->getContext(), true) . '</pre>';
    }

}