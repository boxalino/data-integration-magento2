<?php
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Attribute;

use Boxalino\DataIntegrationDoc\Doc\Attribute;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;

/**
 * Class CustomAttributeAbstract
 *
 * No resource is required for declaring how the new custom attribute must be used/defined in the data index
 *
 * Extend with other properties as defined in the doc_attribute schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280945/doc_attribute
 */
abstract class CustomAttributeAbstract extends Attribute
{

    use DiPropertyTrait;

    /** property code (same as getName) */
    abstract function getCode(): string;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->sanitizePropertyName($this->getCode());
    }

    /**
     * @return string|null
     */
    public function getInternalId(): ?string
    {
        return $this->getCode();
    }


}
