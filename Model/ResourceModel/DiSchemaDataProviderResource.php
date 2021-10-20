<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

/**
 *
 */
class DiSchemaDataProviderResource
{

    use EavResourceTrait;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->adapter = $resource->getConnection();
    }


}
