<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductVisibilityPropertyInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\StringLocalizedAttribute;
use Boxalino\DataIntegrationDoc\Doc\Schema\Visibility as VisibilitySchema;

/**
 * Class Visibility
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#VISIBILITY
 *
 * The visibility is further manipulated at the level of the DocHandler.
 * The children visibility is updated based on the parent it belongs to
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Visibility extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        /** @var DocProductVisibilityPropertyInterface $datProvider */
        $datProvider = $this->getDataProvider();

        foreach ($datProvider->getData() as $item)
        {
            try{
                $content[$this->_getDocKey($item)][$this->getAttributeCode()][] =
                    $this->getSchemaByItem($languages, $datProvider->getContextData($item))->toArray();

                $content[$this->_getDocKey($item)][DocProductPropertyInterface::DOC_SCHEMA_CONTEXTUAL_PROPERTY_PREFIX . $this->getAttributeCode()][] =
                    $this->getSchemaByItem($languages, $datProvider->getAsIsData($item))->toArray();

                $content[$this->_getDocKey($item)][DocSchemaInterface::FIELD_STRING][] =
                    $this->schemaGetter()->getStringAttributeSchema(
                        $datProvider->getIndividualVisibility($item),
                        DocSchemaInterface::FIELD_STRING_INDIVIDUAL_VISIBILITY
                    )->toArray();

                $content[$this->_getDocKey($item)][DocSchemaInterface::FIELD_STRING_LOCALIZED][] = $this->schemaGetter()->getRepeatedGenericLocalizedSchema(
                    $datProvider->getAsIsData($item),
                    $languages,
                    DocSchemaInterface::DI_SCHEMA_CONTEXTUAL_PROPERTY_PREFIX . $this->getAttributeCode(),
                    new StringLocalizedAttribute(), null
                )->toArray();
            } catch (\Throwable $exception)
            {
                $this->logWarning("Error on ". $this->getResolverType() . "with exception: "
                    . $exception->getMessage() . " on " . json_encode($item)
                );
            }
        }

        $this->logInfo(count($content) . " items have content for " . $this->getResolverType());
        return $content;
    }

    /**
     * @param array $languages
     * @param array $data
     * @return VisibilitySchema
     */
    protected function getSchemaByItem(array $languages, array $data) : VisibilitySchema
    {
        return $this->schemaGetter()->getVisibilitySchema($languages, $data);
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_VISIBILITY;
    }


}
