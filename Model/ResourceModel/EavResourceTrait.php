<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav content
 * (joins, selects, etc)
 */
trait EavResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param string $code
     * @param int $type
     * @return string | null
     */
    public function getAttributeIdByAttributeCodeAndEntityType(string $code, int $type) : ?string
    {
        $whereConditions = [
            $this->adapter->quoteInto('attr.attribute_code = ?', $code),
            $this->adapter->quoteInto('attr.entity_type_id = ?', $type)
        ];

        $attributeIdSql = $this->adapter->select()
            ->from(['attr'=>'eav_attribute'], ['attribute_id'])
            ->where(implode(' AND ', $whereConditions));

        $attributeId = $this->adapter->fetchOne($attributeIdSql);
        if(is_bool($attributeId))
        {
            return NULL;
        }

        return $attributeId;
    }

    /**
     * Default function for accessing product attributes values
     * join them with default store
     * and make a selection on the store id
     *
     * @param int $attributeId
     * @param int $storeId
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function getEavJoinAttributeSQLByStoreAttrIdTable(int $attributeId, int $storeId, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->_getEavJoin($attributeId, $storeId, $table, $main);
        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                [
                    'attribute_id' => 'joins.attribute_id',
                    'entity_id' => 'joins.entity_id',
                    'store_id' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR joins.store_value = '', 0, joins.store_id)"),
                    'value' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR joins.store_value = '', joins.default_value, joins.store_value)")
                ]
            );
    }

    /**
     * Default function for accessing product attributes values
     * join them with default store
     * and make a selection on the website (ex: for global or website scoped attributes)
     *
     * @param int $attributeId
     * @param array $storeIds
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function getEavJoinAttributeSQLByStoresAttrIdTable(int $attributeId, array $storeIds, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->_getEavJoin($attributeId, $storeIds, $table, $main);
        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                [
                    'attribute_id'=>'joins.attribute_id',
                    'entity_id' => 'joins.entity_id',
                    'value' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR joins.store_value = '', joins.default_value, joins.store_value)")
                ]
            );
    }

    /**
     * @param int $attributeId
     * @param $storeId
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function _getEavJoin(int $attributeId, $storeId, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->adapter
            ->select()
            ->from(
                ['e' => $main],
                ['entity_id' => 'entity_id']
            );

        $innerCondition = [
            $this->adapter->quoteInto("{$attributeId}_default.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_default.attribute_id = ?", $attributeId),
            $this->adapter->quoteInto("{$attributeId}_default.store_id = ?", 0)
        ];

        if(is_array($storeId))
        {
            $storeId = $this->adapter->quote($storeId);
        }
        $joinLeftConditions = [
            $this->adapter->quoteInto("{$attributeId}_store.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_store.attribute_id = ?", $attributeId),
            $this->adapter->quoteInto("{$attributeId}_store.store_id IN(?)", $storeId)
        ];

        $select
            ->joinInner(
                [$attributeId . '_default' => $table], implode(' AND ', $innerCondition),
                ['default_value' => 'value', 'attribute_id']
            )
            ->joinLeft(
                ["{$attributeId}_store" => $table], implode(' AND ', $joinLeftConditions),
                ["store_value" => 'value', 'store_id']
            );

        return $select;
    }

    /**
     * @param int $storeId
     * @param string $key
     * @return array
     */
    public function getAttributeOptionValuesByStoreAndKey(int $storeId, string $key) : array
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
            )->where('a_o.attribute_id = ?', $key);

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param array $scope list of scopes from \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface
     * @param array $backendType list of available backend_type options (varchar, static, int, decimal, datetime, text)
     * @param array $frontendInput list of available frontend_input options (multiselect, select, text, price, date, textarea, boolean, gallery, media_image, etc)
     * @param bool $orConditional
     * @param int $entityTypeId
     * @return array
     */
    public function getAttributesByScopeBackendTypeFrontendInput(
        array $scope,
        array $backendType = [],
        array $frontendInput = [],
        bool $orConditional = false,
        int $entityTypeId = \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID): array
    {
        $conditions = [];
        if(!empty($backendType))
        {
            $content = $this->adapter->quote($backendType);
            $conditions[] = "e_a.backend_type IN ($content)";
        }
        if(!empty($frontendInput))
        {
            $content = $this->adapter->quote($frontendInput);
            $conditions[] = "e_a.frontend_input IN ($content)";
        }

        if($orConditional && count($conditions) > 1)
        {
            $conditions = [];
            $conditions[] = implode(" OR ", $conditions);
        }

        if(!empty($scope))
        {
            $content = $this->adapter->quote($scope);
            $conditions[] = "c_e_a.is_global IN ($content)";
        }

        $select = $this->adapter->select()
            ->from(
                ['e_a' => $this->adapter->getTableName('eav_attribute')],
                ['attribute_id', 'attribute_code', 'backend_type', 'frontend_input', 'source_model']
            )
            ->joinInner(
                ['c_e_a' => $this->adapter->getTableName('catalog_eav_attribute')],
                'c_e_a.attribute_id = e_a.attribute_id',
                []
            )
            ->where('e_a.entity_type_id = ?', $entityTypeId);

        foreach($conditions as $condition)
        {
            $select->where($condition);
        }

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $attributeCode
     * @param int $entityTypeId
     * @return string|null
     */
    public function getAttributeScopeByAttrCode(
        string $attributeCode,
        int $entityTypeId = \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID) : ?string
    {
        $select = $this->adapter->select()
            ->from(
                ['e_a' => $this->adapter->getTableName('eav_attribute')],
                []
            )
            ->joinInner(
                ['c_e_a' => $this->adapter->getTableName('catalog_eav_attribute')],
                'c_e_a.attribute_id = e_a.attribute_id',
                ['is_global']
            )
            ->where('e_a.entity_type_id = ?', $entityTypeId)
            ->where('e_a.attribute_code= ?', $attributeCode);

        $scope = $this->adapter->fetchOne($select);
        if(is_bool($scope))
        {
            return NULL;
        }

        return $scope;
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
     * @return Select
     */
    public function getAttributeOptionCodeByAttributeIdSelect(int $attributeId) : Select
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

        return $select;
    }


}
