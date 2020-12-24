<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use stdClass;

trait UpdateChildBlockWithDocumentTrait
{
    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     *
     * @return bool
     */
    public function updateChildDocumentWithDocument(string $alias): bool
    {
        return $this->updateChildWith($alias, $this->getDocument());
    }

    /**
     * Update child document to use relative paths
     *
     * @param string $alias
     *
     * @return bool
     */
    public function updateChildDocumentWithContext(string $alias): bool
    {
        return $this->updateChildWith($alias, $this->getContext());
    }

    /**
     * Add the given document to the Magento block as an argument
     *
     * @param string                $alias
     * @param array|stdClass|string $document
     *
     * @return bool
     */
    public function updateChildWith(string $alias, $document): bool
    {
        $block = $this->getChildBlock($alias);

        if (!$block) {
            return false;
        }

        $block->setData('document', $document);

        return true;
    }
}
