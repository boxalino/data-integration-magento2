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
     */
    protected function addValueToAttributeContent(array $data, \ArrayObject &$attributeContent, string $languageCode, bool $addId = false) : void
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
            $content->offsetSet($languageCode, $value);
            $attributeContent->offsetSet($id, $content);
        }
    }


}
