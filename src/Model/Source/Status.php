<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 17:22
 */

namespace Elgentos\PrismicIO\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('Disabled'),
            ],
            [
                'value' => '1',
                'label' => __('Enabled'),
            ],
        ];
    }
}
