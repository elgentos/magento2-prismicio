<?php

namespace Elgentos\PrismicIO\Block\Exception;

class SliceNotFoundException extends BlockException
{
    public const MESSAGE = 'Slices: Slice-type ":slice_type:" not defined in layout for block ":name_in_layout:", but is expected. (:children: are defined)';
}