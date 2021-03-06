<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
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
 * Class implementing testcase for filterb configuration
 *
 * @package Tests
 * @subpackage Domain\Configuration\Filters
 * @author Michael Knoll
 * @author Daniel Lienert
 * @see Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig
 */
class Tx_PtExtlist_Tests_Domain_Configuration_Filters_FilterConfigTest extends Tx_PtExtlist_Tests_BaseTestcase
{
    protected $filterSettings = [];



    protected $configurationBuilderMock = null;



    public function setup()
    {
        parent::setup();

        $this->configurationBuilderMock = Tx_PtExtlist_Tests_Domain_Configuration_ConfigurationBuilderMock::getInstance();
        $this->filterSettings = [
            'breadCrumbString' => 'breadCrumbString',
            'label' => 'testLabel',
            'filterIdentifier' => 'filterName1',
            'filterClassName' => 'test',
            'partialPath' => 'partialPath',
            'ajaxPartialPath' => 'ajaxPartialPath',
            'defaultValue' => 'default',
            'fieldIdentifier' => 'field1',
            'invert' => '1',
            'invertable' => '1',
            'inactiveOption' => '[All]',
            'inactiveValue' => 'inactiveValue',
            'submitOnChange' => '1',
            'renderObj' => [
                'dataWrap' => '{field:allDisplayFields}',
                '_typoScriptNodeValue' => 'TEXT',
            ],
            'renderUserFunctions' => [
                10 => 'EXT:pt_extlist/Resources/Private/UserFunctions/class.tx_ptextlist_demolist_renderer.php:tx_ptextlist_demolist_renderer->iso2CodeRenderer',
            ],
            'accessGroups' => '1,2,3',
        ];
    }



    public function testSetup()
    {
        $this->assertClassExists('Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig');
    }



    /**
     * @return array
     */
    public function fieldIdentifierDataProvider()
    {
        return [
            'singleFieldIdentifier' => [
                'fieldIdentifier' => 'field1',
                'result' => ['field1']
            ],
            'listOfFieldIdentifier' => [
                'fieldIdentifier' => 'field1, field2',
                'result' => ['field1', 'field2']
            ],
            'arrayOfFieldIdentifier' => [
                'fieldIdentifier' => ['10' => 'field3', '20' => 'field4'],
                'result' => ['field3', 'field4']
            ],
            'fieldIdentifierForTimeSpanFilter' => [
                'fieldIdentifier' => [
                    10 => [
                        'start' => 'field1',
                        'end' => 'field2'
                    ],
                    20 => [
                        'start' => 'field3',
                        'end' => 'field4'
                    ]
                ],
                'result' => ['field1', 'field2', 'field3', 'field4']
            ]
        ];
    }



    /**
     * @param $fieldIdentifier
     * @param $result
     * @return void
     * @test
     * @dataProvider fieldIdentifierDataProvider
     */
    public function processAndSetFieldIdentifier($fieldIdentifier, $result)
    {
        $filterSettings = $this->filterSettings;
        $filterSettings['fieldIdentifier'] = $fieldIdentifier;

        $accessibleClassName = $this->buildAccessibleProxy('Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig');
        $accessibleFilterConfig = new $accessibleClassName($this->configurationBuilderMock, $filterSettings, 'test');

        $accessibleFilterConfig->_call('processAndSetFieldIdentifier', $fieldIdentifier);

        $realResult = $accessibleFilterConfig->_get('fieldIdentifier');
        $resultTestList = [];
        foreach ($realResult as $field) {
            $resultTestList[] = $field->getIdentifier();
        }

        $this->assertEquals($resultTestList, $result);
    }



    public function testExceptionOnEmptyFilterIdentifier()
    {
        try {
            $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, [], 'testFilterbox');
        } catch (Exception $e) {
            return;
        }
        $this->fail();
    }



    public function testExceptionOnEmptyPartialPath()
    {
        try {
            $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, ['filterIdentifier' => 'test', 'filterClassName' => 'test'], 'test');
        } catch (Exception $e) {
            return;
        }
        $this->fail('No error has been thrown on non-existing partialPath setting');
    }



    public function testGetDefaultValueSingle()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals($filterConfig->getdefaultValue(), 'default');
    }



    /**
     * @test
     */
    public function getDefaultValueSingleStdWrap()
    {
        $this->markTestSkipped('This test is currently not working in T3 6.1.7 - problem arises in AbstractUserAuthentication.php::904');
        $this->testingFramework->createFakeFrontEnd(0);

        $filterSettings = $this->filterSettings;

        $filterSettings['defaultValue'] = [
            'cObject' => [
                'value' => 'together',
                '_typoScriptNodeValue' => 'TEXT'
            ]
        ];

        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $filterSettings, 'test');

        $this->assertEquals($filterConfig->getdefaultValue(), 'together');
    }



    public function testGetDefaultValueMultiple()
    {
        $this->markTestSkipped('This test is currently not working in T3 6.1.7 - problem arises in AbstractUserAuthentication.php::904');
        $filterSettings = $this->filterSettings;
        $filterSettings['defaultValue'] = [
            10 => 'one',
            20 => 'two',
            '_typoScriptNodeValue' => '',
        ];

        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $filterSettings, 'test');
        $this->assertEquals($filterConfig->getdefaultValue(), [10 => 'one', 20 => 'two']);
    }



    public function testGetDefaultValueMultipleStdWrap()
    {
        $this->markTestSkipped('This test is currently not working in T3 6.1.7 - problem arises in AbstractUserAuthentication.php::904');
        $this->testingFramework->createFakeFrontEnd(0);

        $filterSettings = $this->filterSettings;
        $filterSettings['defaultValue'] = [
            10 => 'one',
            20 => [
                'cObject' => [
                    'value' => 'together',
                    '_typoScriptNodeValue' => 'TEXT',
                ]
            ],
            '_typoScriptNodeValue' => '',
        ];

        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $filterSettings, 'test');
        $this->assertEquals($filterConfig->getdefaultValue(), [10 => 'one', 20 => 'together']);
    }



    public function testGetPartialPath()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertTrue($filterConfig->getPartialPath() == 'partialPath');
    }



    /**
     * @test
     */
    public function getAjaxPartialPath()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertTrue($filterConfig->getAjaxPartialPath() == 'ajaxPartialPath');
    }



    public function testGetInvert()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals(true, $filterConfig->getInvert());
    }



    public function testGetDisableFilterQuery()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals(false, $filterConfig->getDisableFilterQuery());
    }



    public function testGetInvertable()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals(true, $filterConfig->getInvertable());
    }



    public function testGetInactiveOption()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals('[All]', $filterConfig->getInactiveOption());
    }



    public function testGetSubmitOnChange()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals(true, $filterConfig->getSubmitOnChange());
    }



    public function testGetInactiveValue()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals('inactiveValue', $filterConfig->getInactiveValue());
    }



    public function testGetRenderObj()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $renderObj = $filterConfig->getRenderObj();
        $refArray['renderObj'] = 'TEXT';
        $refArray['renderObj.']['dataWrap'] = '{field:allDisplayFields}';

        $this->assertEquals($refArray, $renderObj);
    }



    public function testGetRenderUserFunction()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals($this->filterSettings['renderUserFunctions'], $filterConfig->getRenderUserFunctions());
    }



    public function testGetAccess()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals([1, 2, 3], $filterConfig->getAccessGroups());
    }



    public function testGetBreadCrumbString()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals($filterConfig->getBreadCrumbString(), $this->filterSettings['breadCrumbString']);
    }



    public function testGetLabel()
    {
        $filterConfig = new Tx_PtExtlist_Domain_Configuration_Filters_FilterConfig($this->configurationBuilderMock, $this->filterSettings, 'test');
        $this->assertEquals($filterConfig->getLabel(), $this->filterSettings['label']);
    }
}
