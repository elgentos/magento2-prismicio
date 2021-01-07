<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;
use stdClass;

trait DocumentResolverTrait
{
    /** @var DocumentResolver */
    private $documentResolver;

    /** @var stdClass */
    private $document;

    /**
     * Get the document resolver
     *
     * @return DocumentResolver
     */
    public function getDocumentResolver(): DocumentResolver
    {
        return $this->documentResolver;
    }

    /**
     * Get the context of the reference.
     *
     * @return array|stdClass|string
     */
    public function getContext()
    {
        return $this->getDocumentResolver()
            ->getContext($this->getReference(), $this->getDocument());
    }

    /**
     * Check if the reference has context
     *
     * @return bool
     */
    public function hasContext(): bool
    {
        return $this->getDocumentResolver()
            ->hasContext($this->getReference(), $this->getDocument());
    }

    /**
     * Set the document
     *
     * @param stdClass $document
     *
     * @return void
     */
    public function setDocument(stdClass $document): void
    {
        $this->document = $document;
    }

    /**
     * Get the document
     *
     * @return stdClass|null
     */
    public function getDocument(): ?stdClass
    {
        return $this->document;
    }

    /**
     * Get the reference
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->_getData(BlockInterface::REFERENCE_KEY) ?: '*';
    }

    /**
     * Set the reference
     *
     * @param string $reference
     *
     * @return void
     */
    public function setReference(string $reference): void
    {
        $this->setData(BlockInterface::REFERENCE_KEY, $reference);
    }
}
