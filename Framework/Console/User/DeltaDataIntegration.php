<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\User;

use Boxalino\DataIntegrationDoc\Framework\Console\User\DeltaDataIntegration as BoxalinoDocDeltaDataIntegration;

/**
 * Class DeltaDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:delta:user [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 */
class DeltaDataIntegration extends BoxalinoDocDeltaDataIntegration
{
    
}