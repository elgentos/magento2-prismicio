<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Api\Data;

interface RouteInterface
{
    /**
     * Get the route ID.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get the route title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set the route title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void;

    /**
     * Get the content type.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Set the content type.
     *
     * @param string $contentType
     *
     * @return void
     */
    public function setContentType(string $contentType): void;

    /**
     * Get the route.
     *
     * @return string
     */
    public function getRoute(): string;

    /**
     * Set the route.
     *
     * @param string $route
     *
     * @return void
     */
    public function setRoute(string $route): void;

    /**
     * Get the route status.
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getStatus(): bool;

    /**
     * Set the route status.
     *
     * @param bool $status
     *
     * @return void
     */
    public function setStatus(bool $status): void;

    /**
     * Get the created at date.
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get the updated at date.
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Get the store IDs.
     *
     * @return array
     */
    public function getStoreIds(): array;

    /**
     * Generate uid for requested path
     *
     * @param string $requestPath
     *
     * @return string
     */
    public function getUidForRequestPath(string $requestPath): string;
}
