<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;


use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;

/**
 * Class Translation
 *
 * Loading translation information for every requests attribute
 */
class Translation extends AttributeStrategyAbstract
    implements DocProductPropertyListInterface
{

    /**
     * @var string
     */
    protected $entityAttributeTableType;

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return [
            "name" => "varchar",
            "description" => "text",
            "short_description" => "text"
        ];
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        $attributeId = $this->localizedResourceModel->getAttributeIdByAttributeCodeAndEntityTypeId(
            $this->getAttributeCode(),\Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        if(is_null($attributeId))
        {
            return [];
        }

        $this->setAttributeId((int)$attributeId);
        return $this->getLocalizedDataForAttribute();
    }

    public function getEntityAttributeTableType(): string
    {
        return $this->getAttributes()[$this->getAttributeCode()];
    }


}
