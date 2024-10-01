<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Attribute;

/**
 * Class ReviewSummary
 * Sample for creating new doc_attribute definitions
 *
 * Check Boxalino\DataIntegrationDoc\Doc\Attribute for other functions that can be rewritten
 */
class ReviewSummary extends CustomAttributeAbstract
{

    /**
     * @return string
     */
    public function getCode() : string
    {
        return "review_summary";
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
            return "numeric";
    }

    /**
     * @return bool
     */
    public function isFilterBy(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isOrderBy(): bool
    {
        return true;
    }


}
