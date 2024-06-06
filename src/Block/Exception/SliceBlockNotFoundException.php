<?php

namespace Elgentos\PrismicIO\Block\Exception;

class SliceBlockNotFoundException extends BlockException
{
    public const MESSAGE = 'SliceBlock block ":reference:" not found in layout for block ":name_in_layout:" but is expected.';
}