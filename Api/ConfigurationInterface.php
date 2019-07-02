<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-3-19
 * Time: 10:26
 */

namespace Elgentos\PrismicIO\Api;

use \Magento\Store\Api\Data\StoreInterface;

interface ConfigurationInterface
{
    public const XML_PATH_API_ENABLED = 'prismicio/general/enabled';
    public const XML_PATH_API_ENDPOINT = 'prismicio/general/enpoint';
    public const XML_PATH_API_SECRET = 'prismicio/general/token';

    public const XML_PATH_CONTENT_LANGUAGE = 'prismicio/content/language';
    public const XML_PATH_CONTENT_FETCHLINKS = 'prismicio/content/fetchlinks';
    public const XML_PATH_CONTENT_CONTENT_TYPE = 'prismicio/content/content_type';
    public const XML_PATH_CONTENT_CONTENT_ALLOW_DEBUG = 'prismicio/content/allow_debug';

    public function getApiEnabled(StoreInterface $store): bool;
    public function getApiEndpoint(StoreInterface $store): string;
    public function getApiSecret(StoreInterface $store): string;

    public function getContentLanguage(StoreInterface $store): string;
    public function getFetchLinks(StoreInterface $store): string;
    public function getContentType(StoreInterface $store): string;
    public function allowDebugInFrontend(StoreInterface $store): bool;
}
