<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Repeated;

/**
 * Class Link
 *
 * Exporter for the product link (default: from url_rewrite)
 * Note: the url_key and url_path are part of the VarcharLocalized property handler
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Link extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        /** @var array $item columns: di_id, link, lang1, lang2, lang3 ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            $content[$item[$this->getDiIdField()]][$this->getAttributeCode()][] = $this->getLocalizedSchema($item, $languages);;
        }

        return $content;

    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_LINK;
    }


}
