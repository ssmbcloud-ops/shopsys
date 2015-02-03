<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchConfig;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchService;
use SS6\ShopBundle\Model\AdvancedSearch\RuleData;

class AdvancedSearchServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreateDefaultRuleFormData() {
		$advancedSearchConfigMock = $this->getMock(AdvancedSearchConfig::class, null, [], '', false);
		$filterName = 'filterName';

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$defaultRuleFormData = $advancedSearchService->createDefaultRuleFormViewData($filterName);

		$this->assertArrayHasKey('subject', $defaultRuleFormData);
		$this->assertArrayHasKey('operator', $defaultRuleFormData);
		$this->assertArrayHasKey('value', $defaultRuleFormData);
		$this->assertEquals($filterName, $defaultRuleFormData['subject']);
	}

	public function testGetRulesFormDataByRequestDataDefault() {
		$advancedSearchConfigMock = $this->getMock(AdvancedSearchConfig::class, null, [], '', false);

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$rulesFormViewData = $advancedSearchService->getRulesFormViewDataByRequestData(null);

		$this->assertArrayHasKey(AdvancedSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
		$this->assertCount(2, $rulesFormViewData);
		foreach ($rulesFormViewData as $ruleFormData) {
			$this->assertArrayHasKey('subject', $ruleFormData);
			$this->assertArrayHasKey('operator', $ruleFormData);
			$this->assertArrayHasKey('value', $ruleFormData);
		}
	}

	public function testGetRulesFormDataByRequestData() {
		$advancedSearchConfigMock = $this->getMock(AdvancedSearchConfig::class, null, [], '', false);

		$requestData = [
			[
				'subject' => 'testSubject',
				'operator' => 'testOperator',
				'value' => 'testValue',
			],
		];

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$rulesFormViewData = $advancedSearchService->getRulesFormViewDataByRequestData($requestData);

		$this->assertArrayHasKey(AdvancedSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
		$this->assertCount(2, $rulesFormViewData);
		foreach ($rulesFormViewData as $key => $ruleFormData) {
			if ($key !== AdvancedSearchService::TEMPLATE_RULE_FORM_KEY) {
				$this->assertEquals($requestData[0], $ruleFormData);
			}
		}
	}

	public function testExtendQueryBuilderByAdvancedSearchData() {
		$ruleData = new RuleData('testSubject', 'testOperator', 'testValue');

		$advancedSearchData = [
			AdvancedSearchService::TEMPLATE_RULE_FORM_KEY => null,
			0 => $ruleData,
		];

		$advancedSearchFilterMock = $this->getMockBuilder(AdvancedSearchFilterInterface::class)
			->setMethods(['extendQueryBuilder'])
			->getMockForAbstractClass();

		$advancedSearchConfigMock = $this->getMock(AdvancedSearchConfig::class, ['getFilter'], [], '', false);
		$advancedSearchConfigMock
			->expects($this->once())
			->method('getFilter')
			->with($this->equalTo($ruleData->subject))
			->willReturn($advancedSearchFilterMock);

		$queryBuilderMock = $this->getMock(QueryBuilder::class, null, [], '', false);

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);

		$advancedSearchService->extendQueryBuilderByAdvancedSearchData($queryBuilderMock, $advancedSearchData);
	}
}
