<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 16-4-19
 * Time: 21:27
 */

namespace Elgentos\PrismicIO\Block;

interface BlockInterface
{
    const REFERENCE_KEY = 'reference';

    public function getReference(): string;
    public function setReference(string $reference): void;
}
