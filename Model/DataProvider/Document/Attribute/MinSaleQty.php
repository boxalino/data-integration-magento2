<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Attribute;


/**
 * Class MinSaleQty
 * Sample for creating new doc_attribute definitions
 *
 * Check Boxalino\DataIntegrationDoc\Doc\Attribute for other functions that can be rewritten
 */
class MinSaleQty extends CustomAttributeAbstract
{
	
	/**
	 * @return string
	 */
	public function getCode() : string
	{
		return "min_sale_qty";
	}
	
	/**
	 * @return string
	 */
	public function getFormat(): string
	{
		return "numeric";
	}
	
	
}
