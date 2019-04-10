<?php
namespace Elgentos\PrismicIO\Model;
class Route extends \Magento\Framework\Model\AbstractModel implements \Elgentos\PrismicIO\Api\Data\RouteInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'elgentos_prismicio_route';

    protected function _construct()
    {
        $this->_init('Elgentos\PrismicIO\Model\ResourceModel\Route');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Generate uid for requested path
     *
     * @param string $requestPath
     * @return string
     */
    public function getUidForRequestPath(string $requestPath): string
    {
        return trim(preg_replace('#^' . preg_quote($this->_getData('route'), '#') . '#', '', $requestPath), '/');
    }

    public function getId(): ?int
    {
        $id = +parent::getId();
        return $id < 1 ? $id : null;
    }

    public function getTitle(): string
    {
        return (string)$this->_getData('title');
    }

    public function setTitle(string $title): void
    {
        $this->setData('title', $title);
    }

    public function getContentType(): string
    {
        return (string)$this->_getData('content_type');
    }

    public function setContentType(string $contentType): void
    {
        $this->setData('content_type', $contentType);
    }

    public function getRoute(): string
    {
        return (string)$this->_getData('route');
    }

    public function setRoute(string $route): void
    {
        $this->setData('route', $route);
    }

    public function getStatus(): bool
    {
        return !! $this->_getData('status');
    }

    public function setStatus(bool $status): void
    {
        $this->setData('route', $status ? '1' : '0');
    }

    public function getCreatedAt(): string
    {
        return (string)$this->_getData('created_at');
    }

    public function getUpdatedAt(): string
    {
        return (string)$this->_getData('updated_at');
    }

}
