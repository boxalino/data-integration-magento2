<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\UserSelection;

use Boxalino\DataIntegrationDoc\Framework\Console\UserSelection\DeltaDataIntegration as BoxalinoDocDeltaDataIntegration;

/**
 * Class DeltaDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:delta:user_selection [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 */
class DeltaDataIntegration extends BoxalinoDocDeltaDataIntegration
{

}
