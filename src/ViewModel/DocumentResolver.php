<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use stdClass;

class DocumentResolver implements ArgumentInterface
{
    private const CONTEXT_DELIMITER = '.';

    /** @var CurrentDocument */
    private CurrentDocument $currentDocument;

    /**
     * Constructor.
     *
     * @param CurrentDocument $currentDocument
     */
    public function __construct(
        CurrentDocument $currentDocument
    ) {
        $this->currentDocument = $currentDocument;
    }

    /**
     * Check if the current document has an actual document
     *
     * @return bool
     */
    public function hasDocument(): bool
    {
        return null !== $this->currentDocument->getDocument();
    }

    /**
     * Get the Prismic document from current document
     *
     * @return stdClass
     * @throws DocumentNotFoundException
     */
    public function getDocument(): stdClass
    {
        if (!$this->hasDocument()) {
            throw new DocumentNotFoundException();
        }

        return $this->currentDocument->getDocument();
    }

    /**
     * Check if the document has context
     *
     * @param string        $documentReference
     * @param stdClass|null $document
     *
     * @return bool
     */
    public function hasContext(string $documentReference, stdClass $document = null): bool
    {
        try {
            $this->getContext($documentReference, $document);
        } catch (DocumentNotFoundException | ContextNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the context of the document
     *
     * @return array|stdClass|string
     * @throws ContextNotFoundException
     */
    public function getContext(string $documentReference, ?stdClass $document = null)
    {
        $document ??= $this->getDocument();

        if ($documentReference === '*') {
            return $document;
        }

        $references = explode(self::CONTEXT_DELIMITER, $documentReference);
        $context    = array_reduce(
            $references,
            function ($data, string $reference) {
                if ($data === null) {
                    return null;
                }

                if (is_numeric($reference) && is_array($data)) {
                    return $data[$reference] ?? null;
                }

                return $data->{$reference} ?? null;
            },
            $document
        );

        if (null === $context) {
            throw new ContextNotFoundException();
        }

        return $context;
    }
}
