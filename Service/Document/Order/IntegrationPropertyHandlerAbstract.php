<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderContactPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderItemPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderLineInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocOrderAttributeTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandler;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_order context
 * Handles the data provider access for the export of doc_order properties and structures
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
abstract class IntegrationPropertyHandlerAbstract extends DocSchemaPropertyHandler
    implements \JsonSerializable,
    DocSchemaPropertyHandlerInterface,
    DiIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface
{

    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocOrderAttributeTrait;
    use DiPropertyTrait;

    /**
     * @var DiSchemaDataProviderResolverInterface
     */
    protected $diSchemaDataProviderResolver;

    /**
     * @var DocOrderPropertyInterface | DocOrderLineInterface | DocOrderItemPropertyInterface | DocOrderContactPropertyInterface
     */
    protected $dataProvider;

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
     */
    public function __construct(
        LoggerInterface $logger,
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver,
        array $docAttributePropertiesMapping = []
    ){
        parent::__construct();

        $this->logger = $logger;
        $this->diSchemaDataProviderResolver = $diSchemaDataProviderResolver;

        foreach($docAttributePropertiesMapping as $key=>$name)
        {
            $this->addPropertyNameDocAttributeMapping($key, $name);
        }
    }

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

    /**
     * Connection between the property handler and the data provider
     * (the data provider has the same key in di.xml as the resolver type declared in the property handler)
     *
     * @return string
     */
    abstract public function getResolverType() : string;

    /**
     * The Boxalino Data Structure property name as documented
     * (ex: contacts, items, string_attributes, etc)
     *
     * @return string
     */
    public function getDocSchemaPropertyNode() : string
    {
        return $this->getResolverType();
    }

    /**
     * Artificially created string as identifier in order to be able to array_merge_recursive all contents
     *
     * @param array | string | int $item
     * @return string
     */
    public function _getDocKey($item) : string
    {
        if(is_array($item))
        {
            if(isset($item[$this->getDiIdField()]))
            {
                return "_" . $item[$this->getDiIdField()];
            }

            return (string)$item[$this->getDiIdField()];
        }

        return "_" . $item;
    }

}
