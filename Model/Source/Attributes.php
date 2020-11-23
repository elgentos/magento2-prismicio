<?php declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Attributes implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;
    /**
     * @var CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * Attributes constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->attributeCollectionFactory = $collectionFactory;
    }

    /**
     * @param false $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        /** @var Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        if (!$this->_options) {
            /** @var \Magento\Eav\Model\Attribute $attribute */
            $this->_options = array_map(function ($attribute) {
                return ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getName()];
            }, $attributeCollection->getItems());
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('Please Select')]);
        }

        return $options;
    }
}
