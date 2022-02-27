<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductVisibilityPropertyInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Visibility as VisibilitySchema;

/**
 * Class Visibility
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#VISIBILITY
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Visibility extends IntegrationPropertyHandlerAbstract
{

    public function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        /** @var DocProductVisibilityPropertyInterface $datProvider */
        $datProvider = $this->getDataProvider();
        
        foreach ($datProvider->getData() as $item)
        {
            try{
                $content[$this->_getDocKey($item)][$this->getAttributeCode()] =
                    $this->getSchemaByItem($languages, $datProvider->getContextVisibility($item));

                $content[$this->_getDocKey($item)][DocProductPropertyInterface::DOC_SCHEMA_CONTEXTUAL_PROPERTY_PREFIX . $this->getAttributeCode()] =
                    $this->getSchemaByItem($languages, $datProvider->getSelfVisibility($item));
            } catch (\Throwable $exception)
            {
                if($this->logErrors())
                {
                    $this->logger->warning("Error on ". $this->getResolverType() . "with exception: "
                        . $exception->getMessage() . " on " . json_encode($item)
                    );
                }
            }
        }

        return $content;
    }

    /**
     * @param array $languages
     * @param array $data
     * @return VisibilitySchema
     */
    protected function getSchemaByItem(array $languages, array $data) : VisibilitySchema
    {
        return $this->getVisibilitySchema($languages, $data);
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_VISIBILITY;
    }


}
