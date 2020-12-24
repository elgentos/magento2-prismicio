<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Source\Languages;

use Elgentos\PrismicIO\Model\Source\Languages;

class Fallback extends Languages
{
    /**
     * Return the fallback options as an array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $defaultOption = [
            'value' => '',
            'label' => __('No fallback')
        ];

        $options = parent::toOptionArray();
        array_unshift($options, $defaultOption);

        return $options;
    }
}
