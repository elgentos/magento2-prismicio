<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Source;

use Elgentos\PrismicIO\Model\Api;
use Exception;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ContentTypes implements OptionSourceInterface
{
    /** @var array */
    private $types;

    /** @var Api */
    private $api;

    /**
     * Constructor.
     *
     * @param Api $api
     */
    public function __construct(
        Api $api
    ) {
        $this->api = $api;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $types = $this->types = $this->types ?? $this->getTypes();

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

    /**
     * Return an array with all content types
     *
     * @return array
     */
    public function getTypes(): array
    {
        if (! $this->api->isActive()) {
            return [];
        }

        try {
            return $this->api->create()
                ->getData()
                ->getTypes();
        } catch (Exception $e) {
            return [];
        }
    }
}
