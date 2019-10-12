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
    public function updateChildDocumentWithDocument(string $alias): bool
    {
        return $this->updateChildWith($alias, $this->getDocument());
    }

    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     * @return bool
     * @throws \Elgentos\PrismicIO\Exception\ContextNotFoundException
     * @throws \Elgentos\PrismicIO\Exception\DocumentNotFoundException
     */
    public function updateChildDocumentWithContext(string $alias): bool
    {
        return $this->updateChildWith($alias, $this->getContext());
    }

    public function updateChildWith(string $alias, $document): bool
    {
        $block = $this->getChildBlock($alias);
        if (! $block) {
            return false;
        }

        $block->setDocument($document);
        return true;
    }
}
