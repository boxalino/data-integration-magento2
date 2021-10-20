<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;


/**
 * Class Category
 *
 * Exporting product-category relations
 * Simply exporting the category ids is sufficient
 * (category-relevant information is part of the doc_attribute_value export)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Category extends ModeIntegrator
{

    public function getDataByFieldsWebsite(array $fields, string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_c_p' => $this->adapter->getTableName('catalog_category_product')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "c_p_e_s.entity_id=c_c_p.product_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL")
            ->group("c_c_p.product_id");

        return $this->adapter->fetchAll($select);
    }


}
