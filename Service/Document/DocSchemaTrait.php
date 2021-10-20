<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegrationDoc\Doc\Schema\Localized;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\RepeatedGenericLocalized;
use Boxalino\DataIntegrationDoc\Doc\Schema\RepeatedLocalized;

/**
 * Trait DocSchemaTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DocSchemaTrait
{

    /**
     * @param string $property
     * @param array &$schema
     * @param array $languages
     * @param null $source
     */
    public function addingLocalizedPropertyToSchema(string $property, array &$schema, array $languages, $source = null)
    {
        foreach($languages as $language)
        {
            $content = null;
            if(is_array($source) && isset($source[$language])){ $content = $source[$language]; }
            if(isset($schema[$language]) && !isset($source[$language])){ $content = $schema[$language]; }
            if(is_null($content)){  continue; }

            $localized = new Localized();
            $localized->setValue($content)->setLanguage($language);
            $schema[$property][] = $localized;
        }
    }

    /**
     * @param array $item
     * @param array $languages
     * @param array $values
     * @param string $fieldName
     * @return RepeatedGenericLocalized
     */
    public function getRepeatedGenericLocalized(array $item, array $languages, array $values, string $fieldName = DocSchemaInterface::FIELD_IMAGES) : RepeatedGenericLocalized
    {
        $content = new RepeatedGenericLocalized();
        $schema = new RepeatedLocalized();
        foreach($languages as $language)
        {
            $value = "";
            if(isset($values[$language]))
            {
                $value = $values[$language];
            }
            $localized = new Localized();
            $localized->setLanguage($language)->setValue($value);
            $schema->addValue($localized);
        }
        $schema->setValueId($item[$fieldName]);
        $content->addValue($schema);

        return $content;
    }


}
