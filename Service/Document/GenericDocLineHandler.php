<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\DataProvider\DocSchemaTypedInterface;
use Boxalino\DataIntegration\Api\DataProvider\GenericDocInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\DatetimeLocalizedAttribute;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\NumericLocalizedAttribute;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\StringLocalizedAttribute;
use Boxalino\DataIntegrationDoc\Helper\Generic\DocPropertyGrouping;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingRequiredPropertyException;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Psr\Log\LoggerInterface;

/**
 * Used as a virtualType type for any property handler used for doc_content, doc_user_selection, doc_voucher,..
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
class GenericDocLineHandler extends GenericPropertyHandler
{

    /**
     * @var DocPropertiesInterface
     */
    protected $docSchema;

    /**
     * @var DiSchemaDataProviderInterface | GenericDocInterface
     */
    protected $dataProvider;

    /**
     * @var string
     */
    protected $resolverType;

    /**
     * @param LoggerInterface $logger
     * @param DiSchemaDataProviderInterface $dataProvider
     * @param DocPropertiesInterface $docSchema
     */
    public function __construct(
        LoggerInterface $logger,
        DiSchemaDataProviderInterface $dataProvider,
        DocPropertiesInterface $docSchema,
        string $resolverType
    ) {
        parent::__construct($logger);
        $this->docSchema = $docSchema;
        $this->dataProvider = $dataProvider;
        $this->resolverType = $resolverType;
    }

    /**
     * The returned array must be of type [$resolverType => [<array-for-document-line>]]
     * @return array
     */
    public function _getValues(): array
    {
        $content = [];
        /** @var GenericDocInterface $dataProvider */
        $dataProvider = $this->getDataProvider();
        $propertyNames = $this->getSchemaPropertyNames();
        $languages = $this->getSystemConfiguration()->getLanguages();
        $schemaProperties = new DocPropertyGrouping();

        foreach($dataProvider->getData() as $item)
        {
            try {
                $id = $dataProvider->getId($item);
            } catch (MissingRequiredPropertyException $exception)
            {
                $this->logWarning($exception->getMessage() . " for " . json_encode($item));
                continue;
            }

            $content[$id] = [];
            foreach($item as $propertyName => $value)
            {
                if(empty($value) || is_null($value))
                {
                    continue;
                }

                if($propertyName == $this->getDiIdField())
                {
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getBooleanSchemaTypes()))
                {
                    $content[$id][$propertyName] = (bool)$value;
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getNumericSchemaTypes()))
                {
                    $content[$id][$propertyName] = (float)$value;
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getDatetimeSchemaTypes()))
                {
                    $content[$id][$propertyName] = (string)$dataProvider->sanitizeDateTimeValue($value);
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getSingleValueSchemaTypes()))
                {
                    $content[$id][$propertyName] = (string)$value;
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getMultivalueSchemaTypes()))
                {
                    $content[$id][$propertyName] = explode(",", $value);
                    continue;
                }

                if(in_array($propertyName, $schemaProperties->getLocalizedSchemaProperties()))
                {
                    $localizedData = $dataProvider->get($propertyName, $item);
                    if(empty($localizedData))
                    {
                        $localizedData = $this->schemaGetter()->getLocalizedSchema([$value], $languages);
                    }
                    $content[$id][$propertyName] = $localizedData;
                    continue;
                }
            }

            foreach(array_merge($schemaProperties->getRelationSchemaTypes(), $schemaProperties->getMultivalueSchemaTypes()) as $propertyName)
            {
                /** @var array $values  complete content schema expected */
                $values = $dataProvider->get($propertyName, $item);
                if(empty($values))
                {
                    continue;
                }

                $content[$id][$propertyName] = $values;
                continue;
            }

            foreach($dataProvider->getImages($item) as $optionLabel=>$optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_IMAGES][] = $this->schemaGetter()
                    ->getImagesSchema($optionValues, $optionLabel, $languages)->toArray();
            }

            foreach($dataProvider->getTags($item) as $record)
            {
                try{
                    $content[$id][DocSchemaInterface::FIELD_TAGS][] = $this->schemaGetter()
                        ->getTagSchema($record['value'], $record['type'], $record['loc_values'], $languages)->toArray();
                } catch (\Throwable $exception) {
                    $this->logWarning("Invalid tags data format for $id");
                }
            }

            foreach($dataProvider->getLabels($item) as $record)
            {
                try{
                    $content[$id][DocSchemaInterface::FIELD_LABELS][] = $this->schemaGetter()
                        ->getLabelSchema($record['value'], $record['name'], $record['type'], $record['loc_values'], $languages)->toArray();
                } catch (\Throwable $exception) {
                    $this->logWarning("Invalid labels data format for $id");
                }
            }

            foreach($dataProvider->getPeriods($item) as $record)
            {
                $content[$id][DocSchemaInterface::FIELD_PERIODS][] = $this->schemaGetter()
                    ->getPeriodSchema($record, $languages)->toArray();
            }

            foreach($dataProvider->getStringOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_STRING][] = $this->schemaGetter()
                    ->getStringAttributeSchema($optionValues, $optionLabel)->toArray();
            }

            foreach($dataProvider->getNumericOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_NUMERIC][] = $this->schemaGetter()
                    ->getNumericAttributeSchema($optionValues, $optionLabel, null)->toArray();
            }

            foreach($dataProvider->getDateTimeOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_DATETIME][] = $this->schemaGetter()
                    ->getDatetimeAttributeSchema($optionValues, $optionLabel)->toArray();
            }

            foreach($dataProvider->getLocalizedStringOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_STRING_LOCALIZED][] = $this->schemaGetter()
                    ->getRepeatedGenericLocalizedSchema($optionValues, $languages, $optionLabel, new StringLocalizedAttribute(), null)->toArray();
            }

            foreach($dataProvider->getLocalizedNumericOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_NUMERIC_LOCALIZED][] = $this->schemaGetter()
                    ->getRepeatedGenericLocalizedSchema($optionValues, $languages, $optionLabel, new NumericLocalizedAttribute(), null)->toArray();
            }

            foreach($dataProvider->getLocalizedDateTimeOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_DATETIME_LOCALIZED][] = $this->schemaGetter()
                    ->getRepeatedGenericLocalizedSchema($optionValues, $languages, $optionLabel, new DatetimeLocalizedAttribute(), null)->toArray();
            }
        }

        if(empty($content))
        {
            throw new NoRecordsFoundException("{$this->getLogProcessName()} : No records available for {$this->resolverType}. This is a logical exception in order to exit the handler loop.");
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function getSchemaPropertyNames() : array
    {
        return (new $this->docSchema())->toList();
    }

    /**
     * @return string
     */
    public function getResolverType() : string
    {
        return $this->resolverType;
    }

    /**
     * @return DiSchemaDataProviderInterface
     */
    public function getDataProvider() : DiSchemaDataProviderInterface
    {
        $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
        $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());
        $this->dataProvider->setSyncCheck($this->getSyncCheck());
        $this->dataProvider->setMviewIds($this->getIds());

        $this->dataProvider->resolve();

        return $this->dataProvider;
    }


}
