<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;

/**
 * Helper trait in processing the returned content for the attributes
 */
trait AttributeHelperTrait
{

    /**
     * @var string
     */
    protected $attributeCode;

    /**
     * @var int
     */
    protected $attributeId = 0;

    /**
     * @return string
     */
    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    /**
     * @param string $code
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeCode(string $code): DiSchemaDataProviderInterface
    {
        $this->attributeCode = $code;
        return $this;
    }

    /**
     * @param int $id
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeId(int $id): DiSchemaDataProviderInterface
    {
        $this->attributeId = $id;
        return $this;
    }


}
