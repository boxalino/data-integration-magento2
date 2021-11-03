<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class Media
 * The image data can either be accessed from the catalog_product_entity_varchar
 * Or from catalog_product_entity_media_gallery table
 * The media_gallery attribute is saved this way
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Media extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @param int $storeId
     * @param int $attributeId
     * @return array
     */
    public function getFetchPairsByFieldsWebsiteAttributeId(array $fields, string $websiteId, int $storeId, int $attributeId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $mediaGalleryImageJoin = $this->getMediaGalleryEntitySelect($attributeId, $storeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $mediaGalleryImageJoin->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            );

        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param int $attributeId
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    protected function getMediaGalleryEntitySelect(int $attributeId, int $storeId)
    {
        $select = $this->getFirstImageMediaGalleryEavJoin($attributeId, $storeId);
        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                ['entity_id']
            )
            ->joinLeft(
                ['c_p_e_m_g' => $this->adapter->getTableName('catalog_product_entity_media_gallery')],
                "c_p_e_m_g.value_id = joins.value_id",
                ['value']
            );
    }

    protected function getFirstImageMediaGalleryEavJoin(int $attributeId, int $storeId)
    {
        $select = $this->adapter
            ->select()
            ->from(
                ['e' => 'catalog_product_entity_media_gallery_value_to_entity'],
                ['entity_id']
            )->joinLeft(
                ['e_v' => 'catalog_product_entity_media_gallery'],
                'e_v.value_id = e.value_id',
                ['attribute_id']
            );

        /** @var array $innerCondition  can append with rules for image position */
        $innerCondition = [
            $this->adapter->quoteInto("{$attributeId}_default.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_default.store_id = ?", 0)
        ];

        $joinLeftConditions = [
            $this->adapter->quoteInto("{$attributeId}_store.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_store.store_id IN(?)", $storeId)
        ];

        $select
            ->joinInner(
                [$attributeId . '_default' => "catalog_product_entity_media_gallery_value"],
                implode(' AND ', $innerCondition),
                ['default_value' => 'value_id']
            )
            ->joinLeft(
                ["{$attributeId}_store" => "catalog_product_entity_media_gallery_value"],
                implode(' AND ', $joinLeftConditions),
                ["store_value" => 'value_id']
            );

        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                [
                    'entity_id' => 'joins.entity_id',
                    'value_id' => new \Zend_Db_Expr("IF(joins.store_value IS NULL OR joins.store_value = '', joins.default_value, joins.store_value)")
                ]
            )
            ->where('attribute_id = ?', $attributeId);
    }

}
