<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandler;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_attribute_value context
 * Handles the data provider access for the export of doc_attribute_value content
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
abstract class IntegrationPropertyHandlerAbstract extends DocSchemaPropertyHandler
    implements \JsonSerializable,
    DocSchemaPropertyHandlerInterface,
    DiIntegrationConfigurationInterface
{

    use DiIntegrationConfigurationTrait;

    /**
     * @var DiSchemaDataProviderResolverInterface
     */
    protected $diSchemaDataProviderResolver;

    /**
     * @var DocAttributeValueLineInterface
     */
    protected $dataProvider;

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
     */
    public function __construct(
        LoggerInterface $logger,
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
    ){
        $this->logger = $logger;
        $this->diSchemaDataProviderResolver = $diSchemaDataProviderResolver;

        parent::__construct();
    }

    /**
     * @return DocAttributeValueLineInterface
     */
    public function getDataProvider() : DocAttributeValueLineInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
            $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());

            $this->dataProvider->resolve();
        }

        return $this->dataProvider;
    }

    /**
     * @return string
     */
    abstract public function getResolverType() : string;


}
