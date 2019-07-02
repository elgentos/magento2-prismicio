<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 10:23
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Elgentos\PrismicIO\ViewModel\DocumentResolver;

trait DocumentResolverTrait
{

    /** @var DocumentResolver */
    private $documentResolver;
    /** @var \stdClass */
    private $document;

    /**
     * @return DocumentResolver
     */
    public function getDocumentResolver(): DocumentResolver
    {
        return $this->documentResolver;
    }

    /**
     *
     * @return array|\stdClass|string
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    public function getContext()
    {
        return $this->getDocumentResolver()
                ->getContext($this->getReference(), $this->getDocument());
    }

    public function hasContext(): bool
    {
        return $this->getDocumentResolver()
                ->hasContext($this->getReference(), $this->getDocument());
    }

    public function setDocument(\stdClass $document): void
    {
        $this->document = $document;
    }

    public function getDocument(): ?\stdClass
    {
        return $this->document;
    }

    public function getReference(): string
    {
        return $this->_getData(BlockInterface::REFERENCE_KEY) ?: '*';
    }

    public function setReference(string $reference): void
    {
        $this->setData(BlockInterface::REFERENCE_KEY, $reference);
    }
}
