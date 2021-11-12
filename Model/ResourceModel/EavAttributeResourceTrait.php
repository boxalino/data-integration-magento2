<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;


use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav attribute-related content
 * (joins, selects, etc)
 */
trait EavAttributeResourceTrait
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
    public function getAttributeIdByAttributeCodeAndEntityTypeId(string $code, int $type) : ?string
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
        array $excludeConditions = [],
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
                ["is_global"]
            )
            ->where('e_a.entity_type_id = ?', $entityTypeId);

        foreach($conditions as $condition)
        {
            $select->where($condition);
        }

        foreach($excludeConditions as $condition)
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
     * @param array $fields
     * @param array $frontendInputTypes
     * @return Select
     */
    protected function getEavAttributeByFieldsFrontendInputTypesSelect(array $fields, array $frontendInputTypes) : Select
    {
        return $this->adapter->select()
            ->from(
                $this->adapter->getTableName('eav_attribute'),
                $fields
            )
            ->where('entity_type_id= ?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('frontend_input IN (?)', $frontendInputTypes);
    }

    /**
     * @param array $fields
     * @param array $frontendInputTypes
     * @return array
     */
    public function getFetchPairsAttributeByFieldsFrontendInputTypes(array $fields, array $frontendInputTypes) : array
    {
        $select = $this->getEavAttributeByFieldsFrontendInputTypesSelect($fields, $frontendInputTypes);
        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param array $fields
     * @param array $frontendInputTypes
     * @return array
     */
    public function getFetchColAttributeByFieldsFrontendInputTypes(array $fields, array $frontendInputTypes) : array
    {
        $select = $this->getEavAttributeByFieldsFrontendInputTypesSelect($fields, $frontendInputTypes);
        return $this->adapter->fetchCol($select);
    }


}
