<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

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


}
