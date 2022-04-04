<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing attribute option values content
 * (joins, selects, etc)
 */
trait EavAttributeOptionResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param int $attributeId
     * @param int $storeId
     * @return array
     */
    public function getFetchPairsAttributeOptionValuesByStoreAndAttributeId(int $attributeId, int $storeId) : array
    {
        $select = $this->getAttributeOptionValuesByStoreAndAttributeIdSelect($attributeId, $storeId);
        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param int $attributeId
     * @return array
     */
    public function getFetchPairsAttributeOptionSwatchByAttributeId(int $attributeId) : array
    {
        $select = $this->getAttributeOptionSwatchByStoreAndAttributeIdSelect($attributeId);
        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param int $attributeId
     * @param int $storeId
     * @return Select
     */
    public function getAttributeOptionValuesByStoreAndAttributeIdSelect(int $attributeId, int $storeId) : Select
    {
        return $this->adapter->select()
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
    }

    /**
     * @param int $attributeId
     * @return Select
     */
    public function getAttributeOptionSwatchByStoreAndAttributeIdSelect(int $attributeId) : Select
    {
        return $this->adapter->select()
            ->from(
                ['a_o' => $this->adapter->getTableName('eav_attribute_option')],
                [
                    'option_id',
                    new \Zend_Db_Expr("CASE WHEN c_o.value IS NULL THEN b_o.value ELSE c_o.value END as value")
                ]
            )->joinLeft(
                ['s_o' => $this->adapter->getTableName('eav_attribute_option_swatch')],
                's_o.option_id = a_o.option_id AND s_o.store_id = 0',
                []
            )->where('a_o.attribute_id = ?', $attributeId);
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
