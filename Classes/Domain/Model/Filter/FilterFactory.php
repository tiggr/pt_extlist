<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <lienert@punkt.de>, Michael Knoll <knoll@punkt.de>
*  All rights reserved
*
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/



class Tx_PtExtlist_Domain_Model_Filter_FilterFactory {
	
	/**
	 * Creates an instance of a filter for a given configuration
	 *
	 * @param Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig $filterConfig
	 * @return Tx_PtExtlist_Domain_Model_Filter_FilterInterface
	 */
	public static function createInstanceByFilterConfig(Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig $filterConfig) {
		$filterClassName = $filterConfig->getFilterClassName();
		tx_pttools_assert::isNotEmptyString($filterClassName, array('message' => 'No filter class name given, check configuration!'));
		tx_pttools_assert::isTrue(class_exists($filterClassName), array('message' => 'Given filter class does not exist or is not loaded!'));
		$filter = new $filterClassName($filterConfig->getFilterIdentifier());
		tx_pttools_assert::isTrue(is_a($filter, 'Tx_PtExtlist_Domain_Model_Filter_FilterInterface'), array('message' => 'Given filter class does not implement filter interface!'));
		$filter->injectFilterConfig($filterConfig);
		return $filter;
	}
	
}



?>