<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

/**
 * Helper trait in validating the content format
 */
trait DataValidationTrait
{

    /**
     * @param string|null $date
     * @return string|null
     * @throws \Exception
     */
    public function sanitizeDateTimeValue(?string $date) : ?string
    {
        try{
            list($month, $day, $year) = explode("-", (new \DateTime($date))->format('m-d-Y'));
            if(checkdate((int)$month, (int)$day, (int)$year))
            {
                return (new \DateTime($date))->format("Y-m-d");
            }

            return null;
        } catch (\Throwable $exception)
        {
            return null;
        }
    }


}
