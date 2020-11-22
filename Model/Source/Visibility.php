<?php

namespace Elgentos\PrismicIO\Model\Source;

class Visibility  implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => '1', 'value' => 'Not Visible Individually'],
            ['value' => '2', 'label' => 'Catalog'],
            ['value' => '3', 'label' => 'Search'],
            ['value' => '4', 'label' => 'Catalog, Search']
        ];
    }
}
