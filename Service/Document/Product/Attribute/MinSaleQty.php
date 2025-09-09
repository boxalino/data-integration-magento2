<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;


/**
 *  Exporting MinSaleQty
 *  Requires integration in the clients' integration layer
 */
class MinSaleQty extends  NumericAttributeAbstract
{
	
	/**
	 * @return string
	 */
	public function getResolverType(): string
	{
		return "min_sale_qty";
	}
	
	
}
