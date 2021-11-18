<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

/**
 * Generic resource declaration
 */
class DiSchemaDataProviderResource
{

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
