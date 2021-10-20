<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Image
 *
 * The data provider is returning the required data structure required for generating the content for product main image
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Image extends IntegrationPropertyHandlerAbstract
{
    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        /** @var array $item columns: di_id, images, lang1, lang2, lang3 ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            if($item[DocSchemaInterface::FIELD_INTERNAL_ID])
            {
                $schema = $this->getImagesSchema($item, $languages);
                $content[$item[$this->getDiIdField()]][$this->getResolverType()][] = $schema;
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_IMAGES;
    }


}
