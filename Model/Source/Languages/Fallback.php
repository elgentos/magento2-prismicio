<?php

namespace Elgentos\PrismicIO\Model\Source\Languages;


use Elgentos\PrismicIO\Model\Source\Languages;

class Fallback extends Languages
{

    public function toOptionArray()
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
