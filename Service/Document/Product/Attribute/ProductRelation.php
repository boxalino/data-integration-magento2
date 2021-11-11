<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Product;

/**
 * Class ProductRelation
 *
 * Property to identify configurable connection to other products
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#PRODUCT
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class ProductRelation extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        $content = [];
        foreach($this->getDataProvider()->getData() as $id => $relations)
        {
            $id = $this->_getDocKey($id);

            /** @var \ArrayObject $relation */
            foreach($relations as $relation)
            {
                $schema = new Product();
                $schema->setType((string)$relation->offsetGet("type"))
                    ->setName((string)$relation->offsetGet("name"))
                    ->setProductGroup((string)$relation->offsetGet("product_group"))
                    ->setSku((string)$relation->offsetGet("sku"));

                $content[$id][$this->getResolverType()][] = $schema;
            }
        }
        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRODUCT_RELATIONS;
    }


}
