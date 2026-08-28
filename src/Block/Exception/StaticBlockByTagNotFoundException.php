<?php

namespace Elgentos\PrismicIO\Block\Exception;

class StaticBlockByTagNotFoundException extends BlockException
{
    public const MESSAGE = 'Static Block: Requested Static Block with tag ":tag:" and content type ":content_type:" (lang :language:) not found for block ":name_in_layout:"';
}
