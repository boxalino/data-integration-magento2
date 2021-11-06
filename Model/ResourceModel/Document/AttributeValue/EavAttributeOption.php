<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeOptionResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;

/**
 * Data provider for any product eav-attribute relevant information
 *
 */
class EavAttributeOption extends DiSchemaDataProviderResource
{

    use EavAttributeOptionResourceTrait;
    use EavAttributeResourceTrait;

    /**
     * @param array $frontendInputTypes
     * @return array
     */
    public function getFetchPairsOptionIdAttributeCodeByFrontendInputTypes(array $frontendInputTypes) : array
    {
        $select = $this->adapter->select()
            ->from(
                ['e_a_o' => $this->adapter->getTableName('eav_attribute_option')],
                ['option_id']
            )
            ->joinLeft(
                ['e_a' => $this->adapter->getTableName('eav_attribute')],
                'e_a_o.attribute_id = e_a.attribute_id',
                ['attribute_code']
            )
            ->where('e_a.entity_type_id=?', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('e_a.frontend_input IN (?)', $frontendInputTypes);

        return $this->adapter->fetchPairs($select);
    }


}
