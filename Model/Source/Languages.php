<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 17:22
 */

namespace Elgentos\PrismicIO\Model\Source;

use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\Data\OptionSourceInterface;
use Prismic\Language;

class Languages implements OptionSourceInterface
{
    private array $languages;

    public function __construct(
        public readonly Api $api
    ) {
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $languages = $this->languages ??= $this->getLanguages();

        array_unshift(
            $languages,
            Language::parse(
                (object) [
                    'id'   => '*',
                    'name' => __('Prismic default language'),
                ]
            )
        );

        return array_map(
            static fn (Language $language) => [
                'value' => $language->getId(),
                'label' => $language->getName(),
            ],
            $languages
        );
    }

    public function getLanguages(): array
    {
        if (! $this->api->isActive()) {
            return [];
        }

        try {
            return $this->api->create()
                    ->getData()
                    ->getLanguages();
        } catch (\Exception) {
        }

        return [];
    }
}
