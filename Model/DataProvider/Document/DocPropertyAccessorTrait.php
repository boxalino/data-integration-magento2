<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingRequiredPropertyException;

/**
 * Helper trait in accessing document properties
 * The call is divided as to avoid the lazy loading of the object on each get_class_methods
 */
trait DocPropertyAccessorTrait
{

    /**
     * Isolated to avoid object lazy loading on each call
     * @var array
     */
    protected $classMethods = [];

    /**
     * Identify the getter function for the requested property name
     *
     * @param string $propertyName
     * @param array $row
     * @return mixed
     */
    public function get(string $propertyName, array $row)
    {
        $return = null;
        $functionName = $this->_function_name($propertyName);
        if(in_array($functionName, $this->_class_methods()))
        {
            try{
                $return = $this->$functionName($row);
            } catch (\Throwable $exception)
            {
                if($exception instanceof MissingRequiredPropertyException)
                {
                    throw $exception;
                }

                // do nothing
            }
        }

        return $return;
    }

    /**
     * @param string $propertyName
     * @param string $type
     * @return string
     */
    protected function _function_name(string $propertyName, string $type="get") : string
    {
        $functionSuffix = preg_replace('/\s+/', '', ucwords(implode(" ", explode("_", $propertyName))));

        return $type . $functionSuffix;
    }

    /**
     * @return array
     */
    protected function _class_methods() : array
    {
        if (empty($this->classMethods))
        {
            $this->classMethods = get_class_methods($this);
        }

        return $this->classMethods;
    }



}
