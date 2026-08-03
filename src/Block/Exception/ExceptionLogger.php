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

        if (!$this->configuration->allowExceptions($store)) {
            return;
        }

         // Only throw the exception in developer mode and when opted in for
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
                'reference' => self::gather(static fn () => $block->getReference()),
                'name_in_layout' => $block->getNameInLayout(),
                'children' => self::gather(
                    static fn () => array_keys($block->getLayout()->getChildBlocks($block->getNameInLayout()))
                ),
                'context' => self::gather(static fn () => $block->getContext()),
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

    /**
     * Collect a value for the log context without ever throwing.
     *
     * Blocks like Block\Document replace their document with the resolved context before reporting a
     * missing document, so resolving the reference a second time throws a ContextNotFoundException.
     * That turned a logged warning into a fatal error, in every application mode.
     *
     * @param callable $callback
     * @return mixed
     */
    private static function gather(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable) {
            return null;
        }
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
                $value = \json_encode($value) ?: '';
            }

            $message = str_replace($key, $value, $message);
        }

        return $message;
    }
}