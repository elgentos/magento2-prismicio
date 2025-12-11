<?php
/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Model;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class CacheTypes extends TagScope
{
    /**
     * Prismic documents cache type ID
     */
    public const TYPE_DOCUMENTS = 'prismicio_documents';

    /**
     * Global cache tag for all Prismic documents
     */
    public const TAG_DOCUMENTS = 'PRISMICIO_DOCUMENTS';

    /**
     * Cache tag pattern for specific document items: PRISMICIO_DOC_ITEM_{type}_{uid}
     */
    public const TAG_DOCUMENT_ITEM = 'PRISMICIO_DOC_ITEM_%s_%s';

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct(
            $cacheFrontendPool->get(self::TYPE_DOCUMENTS),
            self::TAG_DOCUMENTS
        );
    }
}
