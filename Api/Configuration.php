<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 11-3-19
 * Time: 10:26
 */

namespace Elgentos\PrismicIO\Api;


use \Magento\Store\Api\Data\StoreInterface;

interface Configuration
{

    const XML_PATH_API_ENDPOINT = 'elgentos_prismicio/general/enpoint';
    const XML_PATH_API_SECRET = 'elgentos_prismicio/general/token';

    public function getApiEndpoint(StoreInterface $store): string;
    public function getApiSecret(StoreInterface $store): string;

}