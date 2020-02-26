<?php
namespace Test\Api\V8;

use ApiTester;
use Codeception\Example;

class GetRecordsCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function _before(ApiTester $I)
    {
        $I->login();
    }
    /**
     * @param ApiTester $I
     * @param Example $example
     *
     * @dataProvider shouldWorkDataProvider
     * @throws \Exception
     */
    public function shouldWork(ApiTester $I, Example $example)
    {
        /** @var \ArrayIterator $iterator */
        $iterator = $example->getIterator();

        $I->sendGET($I->getInstanceURL() . $iterator->offsetGet('endPoint'));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'name' => [
                    'name' => [],
                ],
            ]
        );
    }

    /**
     * @return array
     */
    protected function shouldWorkDataProvider()
    {
        return [
            [
                'endPoint' => '/Api/V8/module/Accounts',
            ],
        ];
    }
}
