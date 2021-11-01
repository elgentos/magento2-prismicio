<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Registry;

use stdClass;

class CurrentDocument
{
    /** @var stdClass */
    private stdClass $document;

    /**
     * Set the current document
     *
     * @param stdClass $document
     *
     * @return void
     */
    public function setDocument(stdClass $document): void
    {
        $this->document = $document;
    }

    /**
     * Get the current document
     *
     * @return stdClass|null
     */
    public function getDocument(): ?stdClass
    {
        return $this->document;
    }
}
