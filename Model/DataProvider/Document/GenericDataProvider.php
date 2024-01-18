<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

use Boxalino\DataIntegration\Api\DataProvider\GenericDocInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingRequiredPropertyException;

/**
 * Class GenericDataProvider
 *
 * A generic model for the data provider for a doc_content, doc_user_selection, doc_user_generated_content, doc_bundle, doc_voucher data type row
 * The ddl are available on github https://github.com/boxalino/data-integration-doc-schema/tree/master/ddl
 * The methods provided in this model are for the
 *
 * Use the `public function resolve()` to preload desired/required data for the typed attributes (string_attributes, string_localized_attributes, etc)
 * The resource used must return database rows with schema matching the integration document
 * (ex: id, type, creation, last_update, persona_id, persona_type, parent_ugc_ids, value, status, stores, title, description, short_description)
 */
class GenericDataProvider implements GenericDocInterface
{
    use ConfigurationHelperTrait;

    /**
     * Call adjacent resources to create related resources (ex: list of key-values) for the document schema
     *
     * @return void
     */
    public function resolve(): void {}

    /**
     * Each data provider should have it`s own `_getData()` functions, based on what the resource provider (resourceModel) offers
     *
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByStoreIdsWebsiteId(
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    public function getId(array $item) : string
    {
        if(isset($item['id']))
        {
            return $item['id'];
        }

        throw new MissingRequiredPropertyException("Document 'id' is not provided");
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>
     * If the property is available in a single language, use `stores` to declare which language is active for
     *
     * @param array $item (simplified format) ["de"=>"<value>", "fr"=>"<value>",..]
     * @return array
     */
    public function getTitle(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>
     * If the property is available in a single language, use `stores` to declare which language is active for
     *
     * @param array $item (simplified format) ["de"=>"<value>", "fr"=>"<value>",..]
     * @return array
     */
    public function getDescription(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>
     * If the property is available in a single language, use `stores` to declare which language is active for
     *
     * @param array $item (simplified format) ["de"=>"<value>", "fr"=>"<value>",..]
     * @return array
     */
    public function getShortDescription(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>
     * If the property is available in a single language, use `stores` to declare which language is active for
     *
     * @param array $item (simplified format) ["de"=>"<value>", "fr"=>"<value>",..]
     * @return array
     */
    public function getLink(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<name STRING, values ARRAY<STRUCT<value_id STRING, value ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>>>>>
     * [["name"=>"", "values"=> [["value_id"=>"", "value"=>[["language"=>"de", "value"=>"<value1>"], ["language"=>"fr", "value"=>"<value2>"], ..]], ..]],[],..]
     *
     * @param array $item (simplified format) ["<name1>"=>["de"=>"<value1>","fr"=>"<value1>",..], "<name2>"=>["de"=>"<value2>","fr"=>"<value2>",..],..]
     * @return array
     */
    public function getImages(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<type STRING NOT NULL, value STRING NOT NULL, loc_values ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>>>
     * [["type"=>"<type1>", "value"=>"<value>", "loc_values"=> [["language"=>"de", "value"=>"<value1>"],[],..]],[],..]
     *
     * @param array $item (simplified format) [["type"=>"<type1>", "value"=>"<value1>", "loc_values"=>["de"=>"<trans1>", "fr"=>"<trans2>",..]],..]
     * @return array
     */
    public function getTags(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<type STRING NOT NULL, name STRING NOT NULL, value STRING NOT NULL, loc_values ARRAY<STRUCT<language STRING NOT NULL, value STRING NOT NULL>>>>
     * [["type"=>"<type1>", "name"=>"<name1>", "value"=>"<value1>", "loc_values"=>[["language"=>"de", "value"=>"<value1>"],[],..]],[],..]
     *
     * @param array $item (simplified format) [["type"=>"<type1>", "name"=>"<name1>", "value"=>"<value1>", "loc_values"=>["de"=>"<trans1>", "fr"=>"<trans2>",..]],..]
     * @return array
     */
    public function getLabels(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * DDL: ARRAY<STRUCT<start_datetime ARRAY<STRUCT<language STRING NOT NULL, value DATETIME NOT NULL>>, end_datetime ARRAY<STRUCT<language STRING NOT NULL, value DATETIME NOT NULL>>>>
     *
     * @param array $item (simplified format) [["start_datetime" => ["de"=>"<value1>", "fr"=>"<value2>"], "end_datetime" => ["de"=>"<value1>", "fr"=>"<value2>"]],[],..]
     * @return array
     */
    public function getPeriods(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Full DDL structure is expected
     *
     * DDL: ARRAY<STRUCT<type STRING, name STRING, product_line STRING, product_group STRING, sku STRING, value NUMERIC>>
     *
     * @param array $item [["type"=>"", "name"=>"", "product_group"=>"", "sku"=>"", "value"=>(float)],[],..]
     * @return array
     */
    public function getProducts(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Full DDL structure is expected
     *
     * DDL: ARRAY<STRUCT<type STRING, name STRING, content_type STRING, content_id STRING, value NUMERIC>>
     *
     * @param array $item [["type"=>"", "name"=>"", "content_type"=>"", "content_id"=>"", "value"=>(float)],[],..]
     * @return array
     */
    public function getContents(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Full DDL structure is expected
     *
     * DDL: ARRAY<STRUCT<type STRING, name STRING, persona_id STRING, customer_id STRING, value NUMERIC>>
     *
     * @param array $item [["type"=>"", "name"=>"", "persona_id"=>"", "customer_id"=>"", "value"=>(float)],[],..]
     * @return array
     */
    public function getCustomers(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as string attributes
     * ex: ["<property_name1>" => [<value1>,<value2>], "<property_name2" => [<value1>,<value2>], .. ]
     * where <value1>,<value2> are string
     *
     * @param array $item
     * @return array
     */
    public function getStringOptions(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as numeric attributes
     * ex: ["<property_name1>" => [<value1>,<value2>], "<property_name2" => [<value1>,<value2>], .. ]
     * where <value1>,<value2> are numeric
     *
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as datetime attributes
     * ex: ["<property_name1>" => [<value1>,<value2>], "<property_name2" => [<value1>,<value2>], .. ]
     * where <value1>,<value2> are formatted times (see $this->sanitizeDateTimeValue())
     *
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as string attributes
     * ex: ["<property_name1>" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], "<property_name2>" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], .. ]
     * where <value1>,<value2> are string
     *
     * @param array $item
     * @return array
     */
    public function getLocalizedStringOptions(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as numeric attributes
     * ex: ["<property_name1>" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], "<property_name2>" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], .. ]
     * where <value1>,<value2> are numeric
     *
     * @param array $item
     * @return array
     */
    public function getLocalizedNumericOptions(array $item) : array
    {
        return [];
    }

    /**
     * NOTE: Simplified schema generation (DDL created in GenericDocLineHandler)
     *
     * Creating a list of label-value elements to be added as datetime attributes
     * ex: ["<property_name1>" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], "<property_name2" => ["de" => [<value1>,<value2>], "fr"=> [<value1>,<value2>], ..], .. ]
     * where <value1>,<value2> are formatted times (see $this->sanitizeDateTimeValue())
     *
     * @param array $item
     * @return array
     */
    public function getLocalizedDateTimeOptions(array $item) : array
    {
        return [];
    }


}
