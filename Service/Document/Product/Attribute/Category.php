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
    public function _getValues() : array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        foreach($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            if(!isset($content[$id]))
            {
                $content[$id][$this->getDocSchemaPropertyNode()] = [];
            }

            if(is_null($item[$this->getAttributeCode()]))
            {
                continue;
            }

            /** @var CategorySchema $schema */
            $schema =  $this->getCategoryAttributeSchema(
                array_filter(explode(",", $item[$this->getAttributeCode()]), 'strlen'),
                $languages
            );

            $content[$id][$this->getDocSchemaPropertyNode()][] = $schema;
        }

        $this->logInfo(count($content) . " items have content for " . $this->getResolverType());

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
