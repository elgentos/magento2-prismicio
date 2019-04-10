<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-3-19
 * Time: 10:29
 */

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration implements ConfigurationInterface
{

    /** @var ScopeConfigInterface */
    private $config;

    public function __construct(
        ScopeConfigInterface $config
    )
    {
        $this->config = $config;
    }

    public function getApiEndpoint(StoreInterface $store): string
    {
        return (string)$this->config->getValue(
                self::XML_PATH_API_ENDPOINT,
                ScopeInterface::SCOPE_STORE,
                $store
        );
    }

    public function getApiSecret(StoreInterface $store): string
    {
        return (string)$this->config->getValue(
                self::XML_PATH_API_SECRET,
                ScopeInterface::SCOPE_STORE,
                $store
        );
    }

    public function getApiEnabled(StoreInterface $store): bool
    {
        return !! $this->config->getValue(
                self::XML_PATH_API_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $store
        );;
    }

    public function getContentLanguage(StoreInterface $store): string
    {
        return (string)$this->config->getValue(
                self::XML_PATH_CONTENT_LANGUAGE,
                ScopeInterface::SCOPE_STORE,
                $store
        ) ?? '*';
    }

}