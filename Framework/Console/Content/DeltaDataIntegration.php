<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\Content;

use Boxalino\DataIntegrationDoc\Framework\Console\Content\DeltaDataIntegration as BoxalinoDocDeltaDataIntegration;

/**
 * Class DeltaDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:delta:content [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 */
class DeltaDataIntegration extends BoxalinoDocDeltaDataIntegration
{

}
