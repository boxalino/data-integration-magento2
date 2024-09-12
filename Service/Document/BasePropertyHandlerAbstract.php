<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BasePropertyHandlerAbstract for any doc_X context
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
abstract class BasePropertyHandlerAbstract extends GenericPropertyHandler
{

    /**
     * @var DiSchemaDataProviderResolverInterface
     */
    protected $diSchemaDataProviderResolver;

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
     */
    public function __construct(
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver,
        array $docAttributePropertiesMapping = [],
        bool $instantMode = false
    ){
        parent::__construct();

        $this->diSchemaDataProviderResolver = $diSchemaDataProviderResolver;
        $this->instantMode = $instantMode;

        foreach($docAttributePropertiesMapping as $key=>$name)
        {
            $this->addPropertyNameDocAttributeMapping($key, $name);
        }
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
