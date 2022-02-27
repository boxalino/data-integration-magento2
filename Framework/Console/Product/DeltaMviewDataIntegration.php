<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\Product;

use Boxalino\DataIntegration\Api\Mview\DiViewHandlerInterface;
use Boxalino\DataIntegration\Framework\Console\AbstractMviewDataIntegration;
use Boxalino\DataIntegration\Framework\Console\MviewIdsExecuteTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\DeltaTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductDeltaIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DeltaMviewDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:delta:mview:product [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 * If the integration deltas are handled by MVIEW - use this event in order to clear the mview after a succesfull sync
 */
class DeltaMviewDataIntegration extends AbstractMviewDataIntegration
{

    use DeltaTrait;
    use ProductTrait;
    use MviewIdsExecuteTrait;

    /**
     * @var ProductDeltaIntegrationHandlerInterface 
     */
    protected $integrationHandler;

    public function __construct(
        ProductDeltaIntegrationHandlerInterface $integrationHandler,
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
        return "Boxalino Delta Mview Product Data Integration.";
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'boxalino:di:delta:mview:product';
    }

    
}
