<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Product;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeValuesHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocLanguagesHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProductHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
class FullIntegrationHandler extends Full
    implements ProductIntegrationHandlerInterface
{

    use ProductTrait;

    public function __construct(
        DocAttributeValuesHandlerInterface $attributeValueHandler,
        DocAttributeHandlerInterface  $attributeHandler,
        DocLanguagesHandlerInterface $languagesHandler,
        DocProductHandlerInterface $productHandler,
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0,
        int $fullConversionThreshold = 0
    ){
        parent::__construct($logger, $docHandlers, $timeout, $fullConversionThreshold);

        $this->addHandler($attributeHandler);
        $this->addHandler($attributeValueHandler);
        $this->addHandler($languagesHandler);
        $this->addHandler($productHandler);
    }


}
