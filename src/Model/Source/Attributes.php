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
    protected array $options = [];

    protected CollectionFactory $attributeCollectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->attributeCollectionFactory = $collectionFactory;
    }

    public function toOptionArray(): array
    {
        /** @var Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();

        if (!$this->options) {
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

        return $this->options;
    }
}
