<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Pricing as DataProviderResourceModel;

/**
 * Class Pricing
 * Accesses the global view of the grouping price
 */
class Pricing extends ModeIntegrator
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
        return $this->resourceModel->getFetchAllByFieldsWebsite(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    /**
     * @param array $row
     * @return string
     */
    public function getLabelForPriceByRow(array $row) : string
    {
        return ($row['min_price'] < $row['max_price']) ? "from" : "";
    }

    public function getFields(): array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_a_s.min_price",
            'c_p_e_a_s.min_price',
            'c_p_e_a_s.max_price',
            'c_p_e_a_s.final_price'
        ];
    }

    function getDataDelta() : array
    {
        return [];
    }


}
