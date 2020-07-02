<?php


namespace Elgentos\PrismicIO\Plugin\ViewModel\LinkResolver;


use Elgentos\PrismicIO\ViewModel\LinkResolver;

class AppendTrailingSlashes
{

    /**
     * To enable this feature just create a module in your project with the next 3 lines
     *
     * <type name="Elgentos\PrismicIO\ViewModel\LinkResolver">
     *     <plugin name="elgentos_prismicio_appendtrailingslashes" type="Elgentos\PrismicIO\Plugin\ViewModel\LinkResolver\AppendTrailingSlashes" />
     * </type>
     */

    /**
     * Apply trailing slashes to all Prismic links
     *
     * @param LinkResolver $linkResolver
     * @param string $url
     * @return string
     */
    public function afterGetFormattedUrl(LinkResolver $linkResolver, string $url): string
    {
        return $url . '/';
    }
}
