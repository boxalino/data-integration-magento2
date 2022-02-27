<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\Product;

use Boxalino\DataIntegration\Api\Mview\DiViewHandlerInterface;
use Boxalino\DataIntegration\Framework\Console\AbstractMviewDataIntegration;
use Boxalino\DataIntegration\Framework\Console\GenericExecuteTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\FullTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FullMviewDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:full:mview:product [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 * If the integration deltas are handled by MVIEW - use this event in order to clear the mview after a succesfull sync
 */
class FullMviewDataIntegration extends AbstractMviewDataIntegration
{

    use FullTrait;
    use ProductTrait;
    use GenericExecuteTrait;

    /**
     * @var ProductIntegrationHandlerInterface 
     */
    protected $integrationHandler;

    public function __construct(
        ProductIntegrationHandlerInterface $integrationHandler,
        DiViewHandlerInterface $diViewHandler,
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        string $mviewViewId,
        ?string $mviewGroupId = "boxalino_di"
    ){
        parent::__construct($diViewHandler, $logger, $configurationManager, $mviewViewId, $mviewGroupId);
        $this->integrationHandler = $integrationHandler;
    }

    public function getDescription(): string
    {
        return "Boxalino Full Mview Product Data Integration.";
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'boxalino:di:full:mview:product';
    }

    
}
