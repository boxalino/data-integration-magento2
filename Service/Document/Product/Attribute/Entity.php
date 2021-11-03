<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Psr\Log\LoggerInterface;

/**
 * Class Entity
 * Access the product_groups & sku information from the product table
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Entity extends IntegrationPropertyHandlerAbstract
{

    public function __construct(
        LoggerInterface $logger,
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver,
        array $docAttributePropertiesMapping = [
            "entity_id" => DocSchemaInterface::FIELD_INTERNAL_ID,
            DocSchemaInterface::FIELD_SKU => DocSchemaInterface::FIELD_SKU,
            "created_at" => DocSchemaInterface::FIELD_CREATION,
            "updated_at"=> DocSchemaInterface::FIELD_UPDATE
        ]
    ){
        parent::__construct($logger, $diSchemaDataProviderResolver, $docAttributePropertiesMapping);
    }

    function getValues(): array
    {
        $content = [];
        foreach($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            $content[$id] = [];
            foreach($item as $propertyName => $value)
            {
                if($propertyName == $this->getDiIdField())
                {
                    continue;
                }

                if($this->handlerHasProperty($propertyName))
                {
                    $docAttributeName = $this->properties[$propertyName];
                    if(in_array($docAttributeName, $this->getProductSingleValueSchemaTypes()))
                    {
                        $content[$id][$docAttributeName] = (string)$value;
                        continue;
                    }
                }

                $content[$id][$propertyName] = (string)$value;
            }
        }

        if(empty($content))
        {
            throw new NoRecordsFoundException("No records available. This is a logical exception in order to exit the handler loop.");
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "entity";
    }


}
