<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

interface BlockInterface
{
    public const REFERENCE_KEY = 'reference';

    /**
     * Get the reference
     *
     * @return string
     */
    public function getReference(): string;

    /**
     * Set the reference
     *
     * @param string $reference
     *
     * @return void
     */
    public function setReference(string $reference): void;
}
