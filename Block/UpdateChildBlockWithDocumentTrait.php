<?php


namespace Elgentos\PrismicIO\Block;


trait UpdateChildBlockWithDocumentTrait
{

    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     * @return bool
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function updateChildDocument(string $alias): bool
    {
        $block = $this->getChildBlock($alias);
        if (! $block) {
            return false;
        }

        $block->setDocument($this->getContext());
        return true;
    }


}
