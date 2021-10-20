<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Type as DataProviderResourceModel;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;

/**
 * Class Type
 */
class Type extends ModeIntegrator
{

    use DiIntegrationConfigurationTrait;

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
        return $this->resourceModel->getDataByFieldsWebsite($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
    }

    public function resolve(): void {}

    /**
     * @return array
     */
    protected function getFields() : array
    {
         return [
             new \Zend_Db_Expr("c_p_e_s.entity_id AS {$this->getDiIdField()}"),
             new \Zend_Db_Expr("c_p_e_s.type_id AS " . $this->getAttributeCode())
         ];
    }

    function getDataDelta() : array
    {
       return [];
    }


}
