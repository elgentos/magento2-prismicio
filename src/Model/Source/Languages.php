<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Source;

use Elgentos\PrismicIO\Model\Api;
use Exception;
use Magento\Framework\Data\OptionSourceInterface;
use Prismic\Language;

class Languages implements OptionSourceInterface
{
    /** @var array */
    private array $languages;

    /** @var Api */
    private Api $api;

    /**
     * Constructor.
     *
     * @param Api $api
     */
    public function __construct(
        Api $api
    ) {
        $this->api = $api;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $languages = $this->languages = $this->languages
            ?? $this->getLanguages();

        return array_map(
            function (Language $language) {
                return [
                    'value' => $language->getId(),
                    'label' => $language->getName()
                ];
            },
            $languages
        );
    }

    /**
     * Get an array of languages
     *
     * @return array
     */
    public function getLanguages(): array
    {
        if (!$this->api->isActive()) {
            return [];
        }

        try {
            return $this->api->create()
                ->getData()
                ->getLanguages();
        } catch (Exception $e) {
            return [];
        }
    }
}
