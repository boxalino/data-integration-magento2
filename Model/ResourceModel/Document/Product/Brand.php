<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeOptionResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;

/**
 * Class Brand
 * Resource for the "manufacturer" attribute code
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Brand extends ModeIntegrator
{

    use EavAttributeOptionResourceTrait;
    use EavAttributeResourceTrait;

    /**
     * Brand data it is mapped 1:1
     * @param array $fields
     * @param string $websiteId
     * @param int $attributeId
     * @return array
     */
    public function getFetchAllByWebsiteAttributeId(array $fields, string $websiteId, int $attributeId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $attributeOptionAdminValueSelect = $this->getAttributeOptionCodeByAttributeIdSelect($attributeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_i' => $this->adapter->getTableName('catalog_product_entity_int')],
                "c_p_e_s.entity_id = c_p_e_i.entity_id AND c_p_e_i.attribute_id= $attributeId",
                []
            )
            ->joinLeft(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $attributeOptionAdminValueSelect->__toString() . ' )')],
                "c_p_e_a_s.option_id = c_p_e_i.value",
                []
            );

        return $this->adapter->fetchAll($select);
    }


}
