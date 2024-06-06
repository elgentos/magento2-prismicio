<?php

namespace Elgentos\PrismicIO\Block\Exception;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Block\BlockInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ExceptionLogger
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ConfigurationInterface $configuration,
        private readonly StoreManagerInterface $storeManager,
    ) {}

    /**
     * @param BlockException $exception
     * @param array $context
     * @return void
     *
     * @throws BlockException
     */
    public function withException(
        BlockException $exception,
        array $context = [],
    ): void {
        $this->logger->debug(
            $exception->getMessage(),
            $context
        );

        $store = $this->storeManager->getStore();
        if (! $this->configuration->allowDebugInFrontend($store)) {
            return;
        }

        // Only throw the exception in developer mode
        throw $exception;
    }

    /**
     * Create a new exception which will be logged and only thrown on production
     *
     * @param string $type
     * @param string $message
     * @param BlockInterface $block
     * @param array $context
     * @return void
     *
     * @throws BlockException
     */
    public static function throwBlockException(
        string $type,
        string $message,
        BlockInterface $block,
        array $context = [],
    ): void {
        $context = array_merge(
            [
                'exception' => $type,
                'reference' => $block->getReference(),
                'name_in_layout' => $block->getNameInLayout(),
                'children' => array_keys($block->getLayout()->getChildBlocks($block->getNameInLayout())),
                'context' => $block->getContext(),
            ],
            $context
        );

        ObjectManager::getInstance()
            ->get(self::class)
            ->withException(
                new $type(self::enhanceMessageWithContext($message, $context)),
                $context
            );
    }

    private static function enhanceMessageWithContext(
        string $message,
        array $context
    ): string {
        foreach ($context as $name => $value) {
            $key = sprintf(':%s:', $name);

            if (! \str_contains($message, $key)) {
                continue;
            }

            if (! \is_string($value)) {
                $value = \json_encode($value, JSON_THROW_ON_ERROR);
            }

            $message = str_replace($key, $value, $message);
        }

        return $message;
    }
}