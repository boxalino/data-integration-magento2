<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;

/**
 * Class Gallery
 * Exporting additional images for the product
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Gallery extends ModeIntegrator
{

    use EavAttributeResourceTrait;
    use EavProductResourceTrait;

    /**
     * @param array $fields
     * @param string $websiteId
     * @param int $storeId
     * @param int $attributeId
     * @return array
     */
    public function getFetchPairsByFieldsWebsite(array $fields, string $websiteId, int $attributeId ) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $mediaGalleryImageJoin = $this->getMediaGalleryEntitySelect($attributeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],

                $fields
            )
            ->joinLeft(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $mediaGalleryImageJoin->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            )
            ->where('c_p_e_a_s.entity_id IS NOT NULL')
            ->group("c_p_e_s.entity_id");

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param int $attributeId
     * @return \Magento\Framework\DB\Select
     */
    protected function getMediaGalleryEntitySelect(int $attributeId) : Select
    {
        return $this->adapter->select()
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity_media_gallery_value_to_entity')],
                ['e.entity_id', "c_p_e_m_g.value"]
            )
            ->joinLeft(
                ['c_p_e_m_g' => $this->adapter->getTableName('catalog_product_entity_media_gallery')],
                "c_p_e_m_g.value_id = e.value_id AND c_p_e_m_g.attribute_id= $attributeId AND c_p_e_m_g.media_type = 'image'",
                []
            )
            ->where("c_p_e_m_g.value IS NOT NULL");
    }


}
