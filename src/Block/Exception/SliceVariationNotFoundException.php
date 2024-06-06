<?php

namespace Elgentos\PrismicIO\Block\Exception;

class SliceVariationNotFoundException extends BlockException
{
    public const MESSAGE = 'Variation ":variation:" not found for block ":name_in_layout:" but is expected. (:children: are defined)';
}