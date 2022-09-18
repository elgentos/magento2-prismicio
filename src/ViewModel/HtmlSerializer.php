<?php


namespace Elgentos\PrismicIO\ViewModel;


use Magento\Framework\View\Element\Block\ArgumentInterface;
use Prismic\Fragment\BlockInterface;

class HtmlSerializer implements ArgumentInterface
{

    /**
     * Html Serializer is sent to rich text parser
     *
     * @param BlockInterface $object
     * @param string $content
     * @return string|null
     */
    public function serialize(BlockInterface $object, string $content): ?string
    {
        return null;
    }

}
