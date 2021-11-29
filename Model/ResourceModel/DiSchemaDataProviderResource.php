<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceTrait;
use Magento\Framework\App\ResourceConnection;

/**
 * Generic resource declaration
 */
class DiSchemaDataProviderResource implements DiSchemaDataProviderResourceInterface
{
    use DiSchemaDataProviderResourceTrait;
    use BaseResourceTrait;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig
    ) {
        $this->adapter = $resource->getConnection();
        $this->deploymentConfig = $deploymentConfig;
    }


}
