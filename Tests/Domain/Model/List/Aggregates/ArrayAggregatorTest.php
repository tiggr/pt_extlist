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



/**
 * Testcase for ArrayAggregator class
 * 
 * @author Daniel Lienert <lienert@punkt.de>
 * @package pt_extlist
 * @subpackage \Tests\Domain\List\Aggregates
 */
class Tx_PtExtlist_Tests_Domain_Model_List_Aggregates_ArrayAggregator_testcase extends Tx_PtExtlist_Tests_BaseTestcase {
	
	protected $testListData;
	
	protected $testData;
	
	public function setUp() {
		$this->testData = array(1,2,3,4,5,6,7,8,9,10);	
		
		$this->initDefaultConfigurationBuilderMock();
		$this->buildTestListData();
	}
	
	public function testGetAggregateByConfig() {
		$aggregateConfigCollection = $this->configurationBuilderMock->buildAggregateDataConfig();
		$aggregateConfig = $aggregateConfigCollection->getAggregateConfigByIdentifier('sumField1');
		
    	$arrayAggregator = new Tx_PtExtlist_Domain_Model_List_Aggregates_ArrayAggregator();  
		$arrayAggregator->injectListData($this->testListData);
		
		$aggregate = $arrayAggregator->getAggregateByConfig($aggregateConfig);
		$aggregate = $arrayAggregator->getAggregateByConfig($aggregateConfig);
		$this->assertEquals(5.5, $aggregate);
	}
	
	
	public function testExceptionIfMethodNotExists() {
		$aggregateConfig = new Tx_PtExtlist_Domain_Configuration_Data_Aggregates_AggregateConfig('sumField2', array('fieldIdentifier' => 'field2', 'method' => 'notExistingMethod'));
		
		$arrayAggregator = new Tx_PtExtlist_Domain_Model_List_Aggregates_ArrayAggregator();
		$arrayAggregator->injectListData($this->testListData);
		try {
			$aggregate = $arrayAggregator->getAggregateByConfig($aggregateConfig);	
		} catch (Exception $e) {
			return;
		}
		
		$this->fail('No Exception thrown if Method not exists');
	}
	
	
	public function testAggregateMethods() {
		$aggregateConfigSum = new Tx_PtExtlist_Domain_Configuration_Data_Aggregates_AggregateConfig('sumField2', array('fieldIdentifier' => 'field2', 'method' => 'sum'));
		$aggregateConfigAvg = new Tx_PtExtlist_Domain_Configuration_Data_Aggregates_AggregateConfig('avgField2', array('fieldIdentifier' => 'field2', 'method' => 'avg'));
		$aggregateConfigMax = new Tx_PtExtlist_Domain_Configuration_Data_Aggregates_AggregateConfig('maxField2', array('fieldIdentifier' => 'field2', 'method' => 'max'));
		$aggregateConfigMin = new Tx_PtExtlist_Domain_Configuration_Data_Aggregates_AggregateConfig('minField2', array('fieldIdentifier' => 'field2', 'method' => 'min'));
		
		
		$accessibleClassName = $this->buildAccessibleProxy('Tx_PtExtlist_Domain_Model_List_Aggregates_ArrayAggregator');
    	$arrayAggregator = new $accessibleClassName;
		
		$arrayAggregator->injectListData($this->testListData);
		$arrayAggregator->_call('buildFieldData','field2');
		
		$sum = $arrayAggregator->_call('getFieldSum','field2');
		$this->assertEquals(array_sum($this->testData), $sum);
		
		$avg = $arrayAggregator->_call('getFieldAvg','field2');
		$this->assertEquals(array_sum($this->testData) / count($this->testData), $avg);
		
		$max = $arrayAggregator->_call('getFieldMax','field2');
		$this->assertEquals(max($this->testData), $max);
		
		$min = $arrayAggregator->_call('getFieldMin','field2');
		$this->assertEquals(min($this->testData), $min);
	}
	
	
	public function testBuildFieldData() {
		$accessibleClassName = $this->buildAccessibleProxy('Tx_PtExtlist_Domain_Model_List_Aggregates_ArrayAggregator');
    	$arrayAggregator = new $accessibleClassName;
    	
    	$arrayAggregator->injectListData($this->testListData);
    	$arrayAggregator->_call('buildFieldData', 'field2');
    	$fieldData = $arrayAggregator->_get('fieldData');
    	$this->assertEquals($this->testData, $fieldData['field2']);
	}
	
	
	protected function buildTestListData() {
		
		$this->testListData = new Tx_PtExtlist_Domain_Model_List_ListData();
		
		foreach($this->testData as $data) {
			$row = new Tx_PtExtlist_Domain_Model_List_Row();
			$row->createAndAddCell($data/10, 'field1');
			$row->createAndAddCell($data, 'field2');
			$row->createAndAddCell($data*10, 'field3');
			$this->testListData->addRow($row);	
		}
	}
	
	
	

}
?>