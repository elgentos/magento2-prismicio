<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use stdClass;

class HtmlSerializer implements ArgumentInterface
{
    /**
     * Html Serializer is sent to rich text parser
     *
     * @param stdClass $object
     * @param string   $content
     *
     * @return string|null
     */
    public function serialize(stdClass $object, string $content): ?string
    {
        return null;
    }
}
