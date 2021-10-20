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
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
    ){
        parent::__construct($logger, $diSchemaDataProviderResolver);

        $this->addPropertyNameDocAttributeMapping("entity_id", DocSchemaInterface::FIELD_INTERNAL_ID);
        $this->addPropertyNameDocAttributeMapping("sku", DocSchemaInterface::FIELD_SKU);
        $this->addPropertyNameDocAttributeMapping("created_at", DocSchemaInterface::FIELD_CREATION);
        $this->addPropertyNameDocAttributeMapping("updated_at", DocSchemaInterface::FIELD_UPDATE);
    }

    function getValues(): array
    {
        $content = [];
        foreach($this->getDataProvider()->getData() as $item)
        {
            $content[$item[$this->getDiIdField()]] = [];
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
                        $content[$item[$this->getDiIdField()]][$docAttributeName] = (string)$value;
                        continue;
                    }
                }

                $content[$item[$this->getDiIdField()]][$propertyName] = $value;
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
