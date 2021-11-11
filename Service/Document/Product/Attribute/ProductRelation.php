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
                $content[$id][$this->getResolverType()][] = $this->_getSchemaByArrayObject($relation);
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

    /**
     * @param \ArrayObject $relation
     * @return Product
     */
    protected function _getSchemaByArrayObject(\ArrayObject $relation) : Product
    {
        $schema = new Product();
        if($relation->offsetExists("type"))
        {
            $schema->setType((string)$relation->offsetGet("type"));
        }
        if($relation->offsetExists("name"))
        {
            $schema->setType((string)$relation->offsetGet("name"));
        }
        if($relation->offsetExists("product_group"))
        {
            $schema->setType((string)$relation->offsetGet("product_group"));
        }
        if($relation->offsetExists("sku"))
        {
            $schema->setType((string)$relation->offsetGet("sku"));
        }

        return $schema;
    }


}
