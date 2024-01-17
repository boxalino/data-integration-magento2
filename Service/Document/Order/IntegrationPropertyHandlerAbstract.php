<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Service\Document\BasePropertyHandlerAbstract;
use Boxalino\DataIntegration\Service\Document\DiDataProviderTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTypedSchemaTrait;

/**
 * The class can be deprecated, it is only left as to avoid telling integrators not declare the diSchemaDataProviderResolver property
 */
abstract class IntegrationPropertyHandlerAbstract extends BasePropertyHandlerAbstract
{
    use DiIntegrateTypedSchemaTrait;
    use DiDataProviderTrait;
    
    
}