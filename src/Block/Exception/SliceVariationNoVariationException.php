<?php

namespace Elgentos\PrismicIO\Block\Exception;

class SliceVariationNoVariationException extends BlockException
{
    public const MESSAGE = 'Variation is not set for the slice for block ":name_in_layout:"';
}