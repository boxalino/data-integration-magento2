<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;

/**
 * Data provider for any product eav-attribute that has a resource model relevant information
 *
 */
class EavAttributeSourceModel extends DiSchemaDataProviderResource
{

    use EavAttributeResourceTrait;

}
