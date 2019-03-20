<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-3-19
 * Time: 10:29
 */

namespace Elgentos\PrismicIO\Model;

use Elgentos\PrismicIO\Api\Configuration as ConfigurationInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration implements ConfigurationInterface
{

    /** @var ReinitableConfigInterface */
    private $config;

    public function __construct(
        ReinitableConfigInterface $config
    )
    {
        $this->config = $config;
    }

    public function getApiEndpoint(StoreInterface $store): string
    {
        return $this->config->getValue(self::XML_PATH_API_ENDPOINT, ScopeInterface::SCOPE_STORE, $store->getCode());
    }

    public function getApiSecret(StoreInterface $store): string
    {
        return $this->config->getValue(self::XML_PATH_API_SECRET, ScopeInterface::SCOPE_STORE, $store->getCode());
    }

}