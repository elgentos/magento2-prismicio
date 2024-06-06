<?php

namespace Elgentos\PrismicIO\Block\Exception;

class StaticBlockNotFoundException extends BlockException
{
    public const MESSAGE = 'Static Block: Requested Static Block ":uid:" with content type ":content_type:" (lang :language:) not found for block ":name_in_layout:"';
}