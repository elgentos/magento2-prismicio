<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 16-4-19
 * Time: 16:46
 */

namespace Elgentos\PrismicIO\ViewModel;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class DocumentResolver implements ArgumentInterface
{
    const CONTEXT_DELIMITER = '.';

    /** @var CurrentDocument */
    private $currentDocument;

    public function __construct(
        CurrentDocument $currentDocument
    ) {
        $this->currentDocument = $currentDocument;
    }

    public function hasDocument(): bool
    {
        return null !== $this->currentDocument->getDocument();
    }

    public function getDocument(): \stdClass
    {
        if (! $this->hasDocument()) {
            throw new DocumentNotFoundException;
        }

        return $this->currentDocument->getDocument();
    }

    public function hasContext(string $documentReference, \stdClass $document = null): bool
    {
        try {
            $this->getContext($documentReference, $document);
        } catch (DocumentNotFoundException $e) {
            return false;
        } catch (ContextNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param string $documentReference
     * @param \stdClass|null $document
     * @return array|\stdClass|string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function getContext(string $documentReference, \stdClass $document = null)
    {
        $document = $document ?? $this->getDocument();
        if ($documentReference === '*') {
            return $document;
        }

        $references = explode(self::CONTEXT_DELIMITER, $documentReference);

        $context = array_reduce($references, function ($data, $reference) {
            if (null === $data) {
                return $data;
            }

            if (is_numeric($reference) && is_array($data)) {
                return $data[$reference] ?? null;
            }

            return $data->{$reference} ?? null;
        }, $document);

        if (null === $context) {
            throw new ContextNotFoundException;
        }

        return $context;
    }
}
