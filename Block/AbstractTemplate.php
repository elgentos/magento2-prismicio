<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-4-19
 * Time: 21:04
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Registry\CurrentDocument;
use Magento\Framework\View\Element\Template;

abstract class AbstractTemplate extends Template
{

    /** @var LinkResolver */
    private $linkResolver;
    /** @var CurrentDocument */
    private $currentDocument;
    /** @var string */
    private $documentReference = '';

    public function __construct(
        Template\Context $context,
        LinkResolver $linkResolver,
        CurrentDocument $currentDocument,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->linkResolver = $linkResolver;
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

    public function setDocumentReference(string $reference): void
    {
        $this->documentReference = $reference;
    }

    public function hasContext(): bool
    {
        try {
            $this->getContext();
        } catch (DocumentNotFoundException $e) {
            return false;
        } catch (ContextNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return array|\stdClass|string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function getContext()
    {
        $document = $this->getDocument();

        if ($this->documentReference === '*') {
            return $document;
        }

        $references = explode('.', $this->documentReference);

        $context = array_reduce($references, function($data, $reference) {
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

    /**
     * @return LinkResolver
     */
    public function getLinkResolver(): LinkResolver
    {
        return $this->linkResolver;
    }

}