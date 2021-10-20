<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;

/**
 * Data provider for any product eav-attribute that has a resource model relevant information
 *
 */
class EavAttributeSourceModel extends DiSchemaDataProviderResource
{

    /**
     * @return array
     */
    public function getSourceModelAttributeCodeMapping() : array
    {
        $frontendInputTypes = ["multiselect", "select"];
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('eav_attribute'),
                ['attribute_code', 'source_model']
            )
            ->where('entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('source_model IS NOT NULL')
            ->where('frontend_input IN (?)', $frontendInputTypes);

        return $this->adapter->fetchPairs($select);
    }

    /**
     * @return array
     */
    public function getAttributeCodes() : array
    {
        $frontendInputTypes = ["multiselect", "select"];
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('eav_attribute'),
                ['attribute_code']
            )
            ->where('entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('source_model IS NOT NULL')
            ->where('frontend_input IN (?)', $frontendInputTypes);

        return $this->adapter->fetchCol($select);
    }


}
