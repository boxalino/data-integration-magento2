<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\ModeIntegratorConditionalsTrait;

abstract class GenericResourceProvider extends DiSchemaDataProviderResource
{

    use ModeIntegratorConditionalsTrait;
    use GenericEntityResourceTrait;

    /**
     * @param array $storeIds
     * @param string $websiteId
     * @return void
     */
    public function getFetchAllByStoreIdsWebsiteId(array $storeIds, string $websiteId) : array
    {
        return $this->adapter->fetchAll($this->getResourceByStoreIdsWebsiteIdSelect($storeIds, $websiteId));
    }


}
