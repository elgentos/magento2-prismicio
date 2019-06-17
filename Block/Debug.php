<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 15-4-19
 * Time: 23:32
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use Elgentos\PrismicIO\ViewModel\LinkResolver;
use Magento\Framework\App\ObjectManager\Environment\Developer;
use Magento\Framework\App\State;
use Magento\Framework\View\Element\Context;

class Debug extends AbstractBlock
{

    /** @var State */
    private $appState;

    public function __construct(
        Context $context,
        DocumentResolver $documentResolver,
        LinkResolver $linkResolver,
        State $appState,
        array $data = []
    ) {
        $this->appState = $appState;

        parent::__construct($context, $documentResolver, $linkResolver, $data);
    }

    public function fetchDocumentView(): string
    {
        if ($this->appState->getMode() !== Developer::MODE) {
            // Only allow debug in developer mode
            return '';
        }

        return '<pre>' . $this->escapeHtml(print_r($this->getContext(), true)) . '</pre>';
    }
}
