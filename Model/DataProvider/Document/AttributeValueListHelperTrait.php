<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Helper trait in processing the returned content for the attributes
 */
trait AttributeValueListHelperTrait
{
    /**
     * @var \ArrayObject
     */
    protected $attributeNameValuesList;

    /**
     * @param string $code
     * @param string $id
     * @return array
     */
    public function getDataByCode(string $code, string $id) : array
    {
        if($this->attributeNameValuesList->offsetExists($code))
        {
            $attributeValues = $this->attributeNameValuesList->offsetGet($code);
            if($attributeValues->offsetExists($id))
            {
                /** @var \ArrayIterator $content */
                $content = $attributeValues->offsetGet($id);
                return $content->getArrayCopy();
            }
        }

        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getDataById(string $id) : array
    {
        if($this->attributeNameValuesList->offsetExists($id))
        {
            /** @var \ArrayIterator $content */
            $content = $this->attributeNameValuesList->offsetGet($id);
            return $content->getArrayCopy();
        }

        return [];
    }

    /**
     * @param array $data
     * @param \ArrayObject $attributeContent
     * @param string $languageCode
     * @param bool $addId
     * @return \ArrayObject
     */
    protected function addValueToAttributeContent(array $data, \ArrayObject $attributeContent, string $languageCode, bool $addId = false) : \ArrayObject
    {
        foreach($data as $id => $value)
        {
            $content = new \ArrayIterator();
            if($addId)
            {
                $content->offsetSet(DocSchemaInterface::DI_ID_FIELD, (string)$id);
            }

            if($attributeContent->offsetExists($id))
            {
                $content = $attributeContent->offsetGet($id);
            }
            $content->offsetSet($languageCode, trim($value));
            $attributeContent->offsetSet($id, $content);
        }

        return $attributeContent;
    }

    /**
     * @param array $data
     * @param \ArrayObject $attributeContent
     * @param string $attributeCode
     * @param bool $addId
     * @param bool $hasList
     * @return \ArrayObject
     */
    protected function addValueTranslationToAttributeContent(array $data, \ArrayObject $attributeContent, string $attributeCode, bool $addId = false, bool $hasList = false) : \ArrayObject
    {
        foreach($data as $row)
        {
            $entityId = $row[$this->getDiIdField()];
            $optionIds = [$row[$attributeCode]];
            if($hasList)
            {
                $optionIds = array_filter(explode(",", $row[$attributeCode]), 'strlen');
            }

            $content = new \ArrayObject();
            foreach($optionIds as $optionId)
            {
                $content = new \ArrayObject();
                if($attributeContent->offsetExists($entityId))
                {
                    $content = $attributeContent->offsetGet($entityId);
                }
                if($content->offsetExists($optionId))
                {
                    continue;
                }

                $optionIdContent = new \ArrayIterator();
                $translation = $this->getDataByCode($attributeCode, $optionId);
                foreach($translation as $languageCode => $value)
                {
                    $optionIdContent->offsetSet($languageCode, $value);
                }
                if($addId)
                {
                    $optionIdContent->offsetSet(DocSchemaInterface::DI_ID_FIELD, (string)$entityId);
                    $optionIdContent->offsetSet($attributeCode, (string)$optionId);
                }

                $content->offsetSet($optionId, $optionIdContent);
            }

            $attributeContent->offsetSet($entityId, $content);
        }

        return $attributeContent;
    }


}
