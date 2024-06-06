<?php

namespace Elgentos\PrismicIO\Block\Exception;

use Elgentos\PrismicIO\Block\BlockInterface;
use Elgentos\PrismicIO\Exception\GeneralException;

abstract class BlockException extends GeneralException
{
    public const MESSAGE = 'Generic Block exception for block ":name_in_layout:"';

    public static function throwException(
        BlockInterface $block,
        array $context = [],
    ): void {
        ExceptionLogger::throwBlockException(
            static::class,
            static::MESSAGE,
            $block,
            $context
        );
    }

}