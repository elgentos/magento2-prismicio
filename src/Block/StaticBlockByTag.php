<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\Block\Exception\StaticBlockByTagNotFoundException;
use Elgentos\PrismicIO\Exception\ApiNotEnabledException;
use Elgentos\PrismicIO\Exception\ContextNotFoundException;
use Elgentos\PrismicIO\Exception\DocumentNotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Resolves its document by content type + Prismic tag instead of by uid - use the "identifier"
 * constructor argument (or "template" reference dot-notation) to pass the tag to search for.
 */
class StaticBlockByTag extends StaticBlock
{
    /**
     * A dynamic tag has no uid to bootstrap a document from, so createPrismicDocument()'s
     * identifier requirement would otherwise block fetchDocumentView() from ever running.
     *
     * @throws NoSuchEntityException
     */
    protected function _toHtml(): string
    {
        if ($this->resolveTag() !== null) {
            return $this->fetchDocumentView();
        }

        return parent::_toHtml();
    }

    /**
     * @throws NoSuchEntityException
     * @throws ApiNotEnabledException
     * @throws ContextNotFoundException
     * @throws DocumentNotFoundException
     */
    protected function fetchChildDocument(): bool
    {
        $tag = $this->resolveTag();
        if ($tag !== null) {
            $type = $this->contentType;
            $lang = $this->api->getOptions()['lang'] ?? '';
        } else {
            $context = $this->getContext();

            // We need to update the document to the current context to change scope for children
            $this->setDocument($context);

            $tag  = $context->uid ?? '';
            $type = $context->type ?? '';
            $lang = $context->lang ?? '';
        }

        $document = $this->api->getDocumentByTag($tag, $type, ['lang' => $lang]);
        if (! $document) {
            StaticBlockByTagNotFoundException::throwException(
                $this,
                [
                    'tag' => $tag,
                    'content_type' => $type,
                    'language' => $lang,
                ]
            );

            return false;
        }

        // Needed to correctly resolve url's
        $document->link_type = 'Document';
        $this->setDocument($document);

        return true;
    }

    /**
     * Override (or plugin - public for that reason) to derive the tag(s) to search for
     * dynamically - e.g. from the current category or product - instead of relying on the
     * "identifier" argument. Returning multiple tags matches a document carrying any of them.
     *
     * @return string|string[]|null
     */
    public function resolveTag(): string|array|null
    {
        return null;
    }
}
