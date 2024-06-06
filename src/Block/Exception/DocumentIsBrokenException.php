<?php

namespace Elgentos\PrismicIO\Block\Exception;

class DocumentIsBrokenException extends BlockException
{
    public const MESSAGE = 'Document is broken so cannot be loaded for block ":name_in_layout:" (context :context:)';
}