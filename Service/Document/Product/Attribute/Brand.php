<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Repeated;

/**
 * Class Brand
 * Load brand information for the product (with translation)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Brand extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        /** @var array $item columns: di_id, brands, lang1, lang2, lang3 ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            if(isset($item[DocSchemaInterface::FIELD_INTERNAL_ID]))
            {
                $item = array_merge(
                    $item,
                    $this->getDataProvider()->getDataById($item[DocSchemaInterface::FIELD_INTERNAL_ID])
                );

                /** @var Repeated $schema value_id is the default store code set for the brand */
                $schema = $this->getRepeatedLocalizedSchema($item, $languages, null, $this->getAttributeCode());
                $content[$this->_getDocKey($item)][$this->getResolverType()][] = $schema;
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_BRANDS;
    }


}
