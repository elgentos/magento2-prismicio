<?php

namespace Elgentos\PrismicIO\Block\Exception;

class GroupExpectsAnArrayException extends BlockException
{
    public const MESSAGE = 'Group expects context to be an array in block ":name_in_layout:"';
}