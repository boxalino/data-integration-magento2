<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandler;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Class BaseIntegrationPropertyHandlerAbstract for any doc_X context
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
abstract class BaseIntegrationPropertyHandlerAbstract extends DocSchemaPropertyHandler
    implements \JsonSerializable,
    DocSchemaPropertyHandlerInterface,
    DiIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocInstantIntegrationInterface,
    DocMviewDeltaIntegrationInterface
{

    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiPropertyTrait;

    /**
     * @var DiSchemaDataProviderResolverInterface
     */
    protected $diSchemaDataProviderResolver;

    /**
     * @var null | bool
     */
    protected $logErrorsFlag = null;

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
     */
    public function __construct(
        LoggerInterface $logger,
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver,
        array $docAttributePropertiesMapping = [],
        bool $instantMode = false
    ){
        parent::__construct();

        $this->logger = $logger;
        $this->diSchemaDataProviderResolver = $diSchemaDataProviderResolver;
        $this->instantMode = $instantMode;

        foreach($docAttributePropertiesMapping as $key=>$name)
        {
            $this->addPropertyNameDocAttributeMapping($key, $name);
        }
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        if($this->filterByIds())
        {
            if($this->hasModeEnabled())
            {
                return $this->_getValues();
            }

            return [];
        }

        return $this->_getValues();
    }

    /**
     * Added an abstract in order to adjust the
     * @return array
     */
    abstract public function _getValues() : array;

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

    /**
     * @return bool
     */
    public function logErrors() : bool
    {
        if(is_null($this->logErrorsFlag))
        {
            $this->logErrorsFlag = $this->getSystemConfiguration()->isTest();
        }

        return $this->logErrorsFlag;
    }


}
