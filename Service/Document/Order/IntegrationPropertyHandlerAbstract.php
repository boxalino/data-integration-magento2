<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderContactPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderItemPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderLineInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Service\Document\BaseIntegrationPropertyHandlerAbstract;
use Boxalino\DataIntegrationDoc\Doc\DocOrderAttributeTrait;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_order context
 * Handles the data provider access for the export of doc_order properties and structures
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
abstract class IntegrationPropertyHandlerAbstract extends BaseIntegrationPropertyHandlerAbstract
{

    use DocOrderAttributeTrait;

    /**
     * @var DocOrderPropertyInterface | DocOrderLineInterface | DocOrderItemPropertyInterface | DocOrderContactPropertyInterface
     */
    protected $dataProvider;

    /**
     * @return DocOrderPropertyInterface | DocOrderLineInterface | DiSchemaDataProviderInterface | DocOrderItemPropertyInterface | DocOrderContactPropertyInterface
     */
    public function getDataProvider() : DocOrderPropertyInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
            $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
            $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());

            $this->dataProvider->resolve();
        }

        return $this->dataProvider;
    }


}
