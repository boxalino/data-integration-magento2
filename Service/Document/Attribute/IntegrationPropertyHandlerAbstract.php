<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandler;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Flow\DiLogTrait;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_attribute context
 * Handles the data provider access for the export of doc_attribute content
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
abstract class IntegrationPropertyHandlerAbstract extends DocSchemaPropertyHandler
    implements DiIntegrationConfigurationInterface
{

    use DiIntegrationConfigurationTrait, DiLogTrait {
        DiIntegrationConfigurationTrait::getDiConfiguration insteadof DiLogTrait;
    }
    use DiPropertyTrait;
    
    /**
     * @var DiSchemaDataProviderResolverInterface
     */
    protected $diSchemaDataProviderResolver;

    /**
     * @var DocAttributeLineInterface
     */
    protected $dataProvider;

    /**
     * @param DiSchemaDataProviderResolverInterface $dataProvider
     */
    public function __construct(
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
    ){
        $this->diSchemaDataProviderResolver = $diSchemaDataProviderResolver;
        parent::__construct();
    }

    /**
     * @return DocAttributeLineInterface
     */
    public function getDataProvider() : DocAttributeLineInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
        }

        $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
        $this->dataProvider->resolve();

        return $this->dataProvider;
    }

    /**
     * @return string
     */
    abstract public function getResolverType() : string;


}
