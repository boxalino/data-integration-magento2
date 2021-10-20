<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\EavResourceTrait;
use Magento\Framework\App\ResourceConnection;

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

    /**
     * @param int $attributeId
     * @param int $storeId
     * @return array
     */
    public function getAttributeOptionValuesByStoreAndAttributeId(int $attributeId, int $storeId) : array
    {
        $select = $this->adapter->select()
            ->from(
                ['a_o' => $this->adapter->getTableName('eav_attribute_option')],
                [
                    'option_id',
                    new \Zend_Db_Expr("CASE WHEN c_o.value IS NULL THEN b_o.value ELSE c_o.value END as value")
                ]
            )->joinLeft(
                ['b_o' => $this->adapter->getTableName('eav_attribute_option_value')],
                'b_o.option_id = a_o.option_id AND b_o.store_id = 0',
                []
            )->joinLeft(
                ['c_o' => $this->adapter->getTableName('eav_attribute_option_value')],
                'c_o.option_id = a_o.option_id AND c_o.store_id = ' . $storeId,
                []
            )->where('a_o.attribute_id = ?', $attributeId);

        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param int $attributeId
     * @return array
     */
    public function getAttributeOptionCodeByAttributeId(int $attributeId) : array
    {
        $select = $this->adapter->select()
            ->from(
                ['a_o' => $this->adapter->getTableName('eav_attribute_option')],
                [
                    'option_id',
                    new \Zend_Db_Expr("b_o.value as value")
                ]
            )->joinLeft(
                ['b_o' => $this->adapter->getTableName('eav_attribute_option_value')],
                'b_o.option_id = a_o.option_id AND b_o.store_id = 0',
                []
            )->where('a_o.attribute_id = ?', $attributeId);

        return $this->adapter->fetchPairs($select);
    }


}
