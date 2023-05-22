<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;


use Boxalino\DataIntegration\Model\ResourceModel\Document\CategoryEntityResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Magento\Framework\DB\Select;

/**
 * Class Category
 *
 * Exporting product-category relations
 * Simply exporting the category ids is sufficient
 * (category-relevant information is part of the doc_attribute_value export)
 *
 * UPDATE:
 * Export only the relations to categories linked to the webstore the account is configured on
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Category extends ModeIntegrator
{
    use CategoryEntityResourceTrait;

    /** @var null | string */
    protected $rootCategoryId = null;

    public function getFetchAllByFieldsWebsite(array $fields, string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->join(
                ['c_c_p' => new \Zend_Db_Expr("( " . $this->getWebsiteCategoryRelationSql()->__toString() . " )")],
                "c_p_e_s.entity_id=c_c_p.product_id",
                []
            )
            ->group("c_c_p.product_id");

        return $this->adapter->fetchAll($select);
    }

    /**
     * @return Select
     */
    protected function getWebsiteCategoryRelationSql() : Select
    {
        $categoryEntitySelect = $this->getEntityColumnByRootCategoryIdSql($this->rootCategoryId, 'path');
        return $this->adapter->select()
            ->from(
                ['c_c_p' => $this->adapter->getTableName('catalog_category_product')],
                ['c_c_p.product_id', 'c_c_p.category_id']
            )
            ->joinLeft(
                ['c_c_e' => new \Zend_Db_Expr("( " . $categoryEntitySelect->__toString() . " )")],
                "c_c_p.category_id = c_c_e.entity_id",
                []
            )
            ->where("c_c_e.entity_id IS NOT NULL");
    }

    /**
     * @param string $rootCategoryId
     * @return DiSchemaDataProviderResourceInterface
     */
    public function setRootCategoryId(string $rootCategoryId) : DiSchemaDataProviderResourceInterface
    {
        $this->rootCategoryId = $rootCategoryId;
        return $this;
    }


}
