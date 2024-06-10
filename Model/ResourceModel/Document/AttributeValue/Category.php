<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\CategoryEntityResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;

/**
 * Data provider for any category-relevant information
 *
 */
class Category extends DiSchemaDataProviderResource
{

    use EavAttributeResourceTrait;
    use CategoryEntityResourceTrait;

    /**
     * @param string $rootCategoryId
     * @return array
     */
    public function getEntityByRootCategoryId(string $rootCategoryId) : array
    {
        return $this->adapter->fetchAll($this->getEntityByRootCategoryIdSql($rootCategoryId));
    }

    /**
     * @param string $rootCategoryId
     * @param string $column
     * @return array
     */
    public function getEntityColumnByRootCategoryId(string $rootCategoryId, string $column) : array
    {
        return $this->adapter->fetchPairs($this->getEntityColumnByRootCategoryIdSql($rootCategoryId, $column));
    }

    /**
     * @param string $attributeName ex: name, description,url_key, url_path, is_active, position, level,
     * @param string $tableName ex: catalog_category_entity_varchar
     * @param int $storeId
     * @return array
     */
    public function getAttributeValueByAttributeTableStoreId(string $attributeName, string $tableName, int $storeId): array
    {
        $attributeId = $this->getAttributeIdByAttributeCodeAndEntityTypeId(
            $attributeName,
            \Magento\Catalog\Setup\CategorySetup::CATEGORY_ENTITY_TYPE_ID);

        $select = $this->adapter->select()
            ->from(
                ['c_t' => $this->adapter->getTableName('catalog_category_entity')],
                ['entity_id']
            )
            ->joinInner(
                ['c_v_i' => $this->adapter->getTableName($tableName)],
                'c_v_i.entity_id = c_t.entity_id AND c_v_i.store_id = 0 AND c_v_i.attribute_id = ' . $attributeId,
                ['value_default' => 'c_v_i.value']
            )
            ->joinLeft(
                ['c_v_l' => $this->adapter->getTableName($tableName)],
                'c_v_l.entity_id = c_t.entity_id AND c_v_l.attribute_id = ' . $attributeId . ' AND c_v_l.store_id = ' . $storeId,
                ['c_v_l.value', 'c_v_l.store_id']
            );

        $selectSql = $this->adapter->select()
            ->from(
                array('joins' => new \Zend_Db_Expr("( " . $select->__toString() . ")")),
                array(
                    'entity_id' => 'joins.entity_id',
                    new \Zend_Db_Expr("IF (joins.value IS NULL OR joins.value='', TRIM(joins.value_default), TRIM(joins.value)) AS value")
                )
            );

        return $this->adapter->fetchPairs($selectSql);
    }


    /**
     * @param string $entityType
     * @param int $storeId
     * @return array
     */
    public function getUrlRewriteByTypeStoreId(string $entityType, int $storeId) : array
    {
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('url_rewrite'),
                ["entity_id", "request_path"]
            )
            ->where('entity_type=?', $entityType)
            ->where('store_id=?', $storeId)
            ->group('entity_id');

        return $this->adapter->fetchPairs($select);
    }


}
