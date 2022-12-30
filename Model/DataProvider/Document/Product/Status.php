<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Status as DataProviderResourceModel;

/**
 * Class Status
 * If the status is configurable only at the level of WEBSITE OR GLOBAL - the property is exported as GLOBAL
 * If the status is configurable at the STORE level - the property is exported as LOCALIZED
 *
 * For grouped / configurable products - the product_group status depends on the availability of children to be bought
 * (ex: status, stock, etc)
 *
 * This logic is configurable
 */
class Status extends ModeIntegrator
{


    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }
    
    /**
     * @return array
     */
    public function _getData(): array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->getResourceModel()->getFetchPairsByFieldsWebsiteStore(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId
            );

            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.value"
        ];
    }



}
