<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 17-4-19
 * Time: 10:23
 */

namespace Elgentos\PrismicIO\Block;

use Elgentos\PrismicIO\ViewModel\LinkResolver;

trait LinkResolverTrait
{

    /** @var LinkResolver */
    private $linkResolver;

    public function getLinkResolver(): LinkResolver
    {
        return $this->linkResolver;
    }
}
