<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Status
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#STATUS
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Status extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_STATUS;
    }


}
