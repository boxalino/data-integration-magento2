<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\UserGeneratedContent;

use Boxalino\DataIntegrationDoc\Framework\Console\UserGeneratedContent\FullDataIntegration as BoxalinoDocFullDataIntegration;

/**
 * Class FullDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:full:user_generated_content [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 */
class FullDataIntegration extends BoxalinoDocFullDataIntegration
{

}
