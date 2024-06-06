<?php

namespace Elgentos\PrismicIO\Block\Exception;

class DocumentIsNotFoundException extends BlockException
{
    public const MESSAGE = 'Document is not found so cannot be loaded for block ":name_in_layout:" (context :context:)';
}