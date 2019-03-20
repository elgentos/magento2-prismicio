<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 20-3-19
 * Time: 13:59
 */

namespace Elgentos\PrismicIO\Controller\Adminhtml\Routes;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreRepository;
use Prismic\Api;

class Index extends Action
{


    /** @var PageFactory */
    private $pageFactory;
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }

}