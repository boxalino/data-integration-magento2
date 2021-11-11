<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

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
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @param DataProviderResourceModel $resource
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
        return $this->resourceModel->getFetchAllByFieldsWebsite($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
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
             new \Zend_Db_Expr("GROUP_CONCAT(c_p_r.type_id SEPARATOR ',') AS " . DocSchemaInterface::DI_PARENT_ID_TYPE_FIELD),
             'c_p_e.entity_id',
             "c_p_e.sku",
             "c_p_e.created_at",
             "c_p_e.updated_at"
         ];
    }

    public function getDataDelta() : array
    {
        return [];
    }

}
