<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Type as DataProviderResourceModel;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;

/**
 * Class Type
 */
class Type extends ModeIntegrator
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

    public function resolve(): void {}

    /**
     * @return array
     */
    public function getFields() : array
    {
         return [
             $this->getDiIdField() => "c_p_e_s.entity_id",
             $this->getAttributeCode() => "c_p_e_s.type_id"
         ];
    }

    function getDataDelta() : array
    {
       return [];
    }


}
