<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 17:22
 */

namespace Elgentos\PrismicIO\Model\Source;

use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\Data\OptionSourceInterface;

class ContentTypes implements OptionSourceInterface
{

    /** @var array */
    private $types;
    /** @var Api */
    private $api;

    public function __construct(
        Api $api
    ) {
        $this->api = $api;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $types = $this->types
                = $this->types ?? $this->getTypes();

        return array_map(
            function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label
                ];
            },
            $types,
            array_keys($types)
        );
    }

    public function getTypes(): array
    {
        if (! $this->api->isActive()) {
            return [];
        }

        try {
            return $this->api->create()
                    ->getData()
                    ->getTypes();
        } catch (\Exception $e) {
        }

        return [];
    }
}
