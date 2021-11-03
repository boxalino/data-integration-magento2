<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;

/**
 * Data provider for any product eav-attribute relevant information
 *
 */
class EavAttributeOption extends DiSchemaDataProviderResource
{

    /**
     * @return array
     */
    public function getOptionSelectAttributes()
    {
        $frontendInputTypes = ["multiselect", "select"];
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('eav_attribute'),
                ['attribute_id', 'attribute_code']
            )
            ->where('entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('frontend_input IN (?)', $frontendInputTypes);

        return $this->adapter->fetchPairs($select);
    }

    /**
     * @return array
     */
    public function getOptionIdAttributeCodeMapping() : array
    {
        $frontendInputTypes = ["multiselect", "select"];
        $select = $this->adapter->select()
            ->from(
                ['e_a_o' => $this->adapter->getTableName('eav_attribute_option')],
                ['option_id']
            )
            ->joinLeft(
                ['e_a' => $this->adapter->getTableName('eav_attribute')],
                'e_a_o.attribute_id = e_a.attribute_id',
                ['attribute_code']
            )
            ->where('e_a.entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('e_a.frontend_input IN (?)', $frontendInputTypes);

        return $this->adapter->fetchPairs($select);
    }


}
