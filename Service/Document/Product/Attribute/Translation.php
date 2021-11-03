<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Translation
 *
 * Loads macro-properties declared for all doc_product levels
 * (title, description & short_description)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Translation extends IntegrationPropertyHandlerAbstract
{

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver
     * @param array $docAttributePropertiesMapping
     */
    public function __construct(
        LoggerInterface$logger,
        DiSchemaDataProviderResolverInterface $diSchemaDataProviderResolver,
        array $docAttributePropertiesMapping = [
            "name" => DocSchemaInterface::FIELD_TITLE,
            DocSchemaInterface::FIELD_DESCRIPTION => DocSchemaInterface::FIELD_DESCRIPTION,
            DocSchemaInterface::FIELD_SHORT_DESCRIPTION => DocSchemaInterface::FIELD_SHORT_DESCRIPTION
        ]
    ){
        parent::__construct($logger, $diSchemaDataProviderResolver, $docAttributePropertiesMapping);
    }

    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        foreach ($this->getDataProvider()->getAttributes() as $attributeCode => $type)
        {
            $this->getDataProvider()->setAttributeCode($attributeCode);

            /** @var array $item columns: di_id, <attributeCode>, lang1, lang2, lang3 .. */
            foreach ($this->getDataProvider()->getData() as $item) {
                if ($item instanceof \ArrayIterator) {
                    $item = $item->getArrayCopy();
                }

                $id = $this->_getDocKey($item);
                if (!isset($content[$id])) {
                    $content[$id][$this->getDocPropertyByField($attributeCode)] = [];
                }

                $content[$id][$this->getDocPropertyByField($attributeCode)] = $this->getLocalizedSchema($item, $languages);
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "translation";
    }


}
