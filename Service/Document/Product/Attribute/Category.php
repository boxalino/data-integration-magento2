<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Category as CategorySchema;

/**
 * Class Category
 * Category is the only hierarchical property
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Category extends IntegrationPropertyHandlerAbstract
{

    /**
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        foreach($this->getDataProvider()->getData() as $item)
        {
            if(!isset($content[$item[$this->getDiIdField()]]))
            {
                $content[$item[$this->getDiIdField()]][$this->getDocSchemaPropertyNode()] = [];
            }

            if(is_null($item[$this->getAttributeCode()]))
            {
                continue;
            }

            /** @var CategorySchema $schema */
            $schema =  $this->getCategoryAttributeSchema(
                explode(",", $item[$this->getAttributeCode()]),
                $languages
            );

            $content[$item[$this->getDiIdField()]][$this->getDocSchemaPropertyNode()][] = $schema;
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_CATEGORIES;
    }


}
