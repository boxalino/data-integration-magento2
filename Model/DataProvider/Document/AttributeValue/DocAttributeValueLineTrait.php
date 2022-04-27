<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValueListHelperTrait;

/**
 * Helper trait for default behaviors on doc_attribute_value line
 */
trait DocAttributeValueLineTrait
{

    use AttributeValueListHelperTrait;

    /**
     * @var string
     */
    protected $attributeCode;

    /**
     * @param string $id
     * @return string
     */
    public function getAttributeName(string $id): string
    {
        return $this->attributeCode;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isNumerical(string $id): bool
    {
        return false;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getValueLabel(string $id): array
    {
        return $this->getDataByCode($this->attributeCode, $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getParentValueIds(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getShortDescription(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getDescription(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getImages(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getLink(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getStatus(string $id): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return string | null
     */
    public function getKey(string $id) : ?string
    {
        try{
            $content = $this->getDataByCode($this->attributeCode, $id);
            if(isset($content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_KEY]))
            {
                return $content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_KEY];
            }

            return null;
        } catch (\Throwable $exception)
        {
            return null;
        }
    }

    /**
     * @param string $id
     * @return string | null
     */
    public function getSwatch(string $id) : ?string
    {
        try{
            $content = $this->getDataByCode($this->attributeCode, $id);
            if(isset($content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_SWATCH]))
            {
                return $content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_SWATCH];
            }

            return null;
        } catch (\Throwable $exception)
        {
            return null;
        }
    }

    /**
     * @param string $id
     * @return string | null
     */
    public function getSortOrder(string $id) : ?string
    {
        try{
            $content = $this->getDataByCode($this->attributeCode, $id);
            if(isset($content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_SORT_ORDER]))
            {
                return (string)$content[DocAttributeValueLineInterface::STRING_ATTRIBUTES_SORT_ORDER];
            }

            return null;
        } catch (\Throwable $exception)
        {
            return null;
        }
    }


}
