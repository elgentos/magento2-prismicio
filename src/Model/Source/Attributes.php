<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Eav\Model\Attribute;
use Magento\Framework\Option\ArrayInterface;

class Attributes implements ArrayInterface
{
    /** @var array|null */
    protected $options;

    /** @var CollectionFactory */
    protected $attributeCollectionFactory;

    /**
     * Attributes constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->attributeCollectionFactory = $collectionFactory;
    }

    /**
     * Return the attributes as an array
     *
     * @param bool $isMultiselect
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        /** @var Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();

        if (!$this->options) {
            /** @var Attribute $attribute */
            $this->options = array_map(
                function (Attribute $attribute) {
                    return [
                        'value' => $attribute->getAttributeCode(),
                        'label' => $attribute->getName()
                    ];
                },
                $attributeCollection->getItems()
            );
        }

        $options = $this->options;

        if (!$isMultiselect) {
            array_unshift(
                $options,
                ['value' => '', 'label' => __('Please Select')]
            );
        }

        return $options;
    }
}
