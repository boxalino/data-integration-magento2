<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Attribute;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;

/**
 * Data provider for any product eav-attribute relevant information
 *
 */
class EavAttribute extends DiSchemaDataProviderResource
{
    /**
     * @return array
     */
    public function getEavAttributes(): array
    {
        $select = $this->adapter->select()
            ->from(
                ['c_e_a' => $this->adapter->getTableName('catalog_eav_attribute')],
                ["*"]
            )
            ->joinLeft(
                ['e_a' => $this->adapter->getTableName('eav_attribute')],
                'c_e_a.attribute_id = e_a.attribute_id',
                ['attribute_code', 'backend_type', 'frontend_input', 'frontend_label']
            )
            ->joinLeft(
                ['e_e_a' => $this->adapter->getTableName('eav_entity_attribute')],
                'e_e_a.attribute_id = c_e_a.attribute_id',
                []
            )
            ->joinLeft(
                ['e_a_g' => $this->adapter->getTableName('eav_attribute_group')],
                'e_a_g.attribute_group_id = e_e_a.attribute_group_id',
                ['attribute_group_code']
            )
            ->where('e_a.entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->group("c_e_a.attribute_id");

        return $this->adapter->fetchAll($select);
    }



}
