<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as DataProviderResourceModel;

/**
 * Class AttributeOptionLocalized
 */
class AttributeOption extends ModeIntegrator
    implements DocProductPropertyListInterface
{

    use AttributeOptionTrait;

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->optionResourceModel = $resource;
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     * di_id, <list of option ids>
     *
     * @return array
     */
    public function _getData() : array
    {
        return $this->getEntityOptionAttributeData();
    }

    /**
     * Looping through attribute-option properties
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->optionResourceModel->getAttributesByScopeBackendTypeFrontendInput(
            [],
            $this->getBackendTypeList(),
            $this->getFrontendInputList()
        );
    }

    /**
     * load the data provider with the translated option
     */
    public function resolve(): void
    {
        foreach($this->getAttributes() as $attribute)
        {
            $this->attributeNameValuesList->offsetSet(
                $attribute["attribute_code"],
                $this->getLocalizedOptionValuesByAttributeId($attribute['attribute_id'])
            );
        }
    }

    public function getBackendTypeList() : array
    {
        return ["int"];
    }

    public function getFrontendInputList() : array
    {
        return ["select"];
    }


}
