<?php

namespace Elgentos\PrismicIO\Block\Exception;

class ContentRelationshipNotFoundException extends BlockException
{
    public const MESSAGE = 'Content Relationship: Document type ":document_type:" not defined in layout for block ":name_in_layout:", but is expected. (:children: are defined)';
}
