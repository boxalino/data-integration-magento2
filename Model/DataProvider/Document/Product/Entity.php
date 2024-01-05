<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Entity as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProductHandlerInterface;

/**
 * Class Entity
 * The products exported are as follows:
 * - belong to the website the account is linked to (both parent and child)
 *
 */
class Entity extends ModeIntegrator
{

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsWebsite($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
         return [
             new \Zend_Db_Expr("IF(c_p_r.parent_id IS NULL, '" . DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP . "', '" .  DocProductHandlerInterface::DOC_PRODUCT_LEVEL_SKU . "') AS " . DocSchemaInterface::DI_DOC_TYPE_FIELD),
             new \Zend_Db_Expr("c_p_e.entity_id AS {$this->getDiIdField()}"),
             new \Zend_Db_Expr("GROUP_CONCAT(c_p_r.parent_id SEPARATOR ',') AS " . DocSchemaInterface::DI_PARENT_ID_FIELD),
             new \Zend_Db_Expr("GROUP_CONCAT(c_p_r.parent_type_id SEPARATOR ',') AS " . DocSchemaInterface::DI_PARENT_ID_TYPE_FIELD),
             new \Zend_Db_Expr("IF(c_p_r_p.child_id IS NULL, NULL, '" .  DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP . "') AS " . DocSchemaInterface::DI_AS_VARIANT),
             new \Zend_Db_Expr("c_p_e.entity_id AS " . DocSchemaInterface::FIELD_EXTERNAL_ID),
             'c_p_e.entity_id',
             "c_p_e.sku",
             new \Zend_Db_Expr("TRIM(LEADING '-' FROM c_p_e.created_at) AS created_at"),
             new \Zend_Db_Expr("TRIM(LEADING '-' FROM c_p_e.updated_at) AS updated_at"),
             "c_p_e.type_id",
             "c_p_e.has_options"
         ];
    }


}
