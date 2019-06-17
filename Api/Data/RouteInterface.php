<?php

namespace Elgentos\PrismicIO\Api\Data;

interface RouteInterface
{
    public function getId(): ?int;

    public function getTitle(): string;
    public function setTitle(string $title): void;

    public function getContentType(): string;
    public function setContentType(string $contentType): void;

    public function getRoute(): string;
    public function setRoute(string $route): void;

    public function getStatus(): bool;
    public function setStatus(bool $status): void;

    public function getCreatedAt(): string;
    public function getUpdatedAt(): string;

    public function getStoreIds(): array;

    /**
     * Generate uid for requested path
     *
     * @param string $requestPath
     * @return string
     */
    public function getUidForRequestPath(string $requestPath): string;
}
