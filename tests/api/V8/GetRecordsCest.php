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
                'data' => [
                    'meta' => [
                        'type' => 'Account',
                        'id' => '22ad4c3d-dcff-0ec9-5f16-5e569fd3e780',
                        'attributes' => ['"name" => "Southern Realty",
                                        "date_entered" => "2020-02-26T16 =>38 =>00+00 =>00",
                                        "date_modified" => "2020-03-03T09 =>34 =>00+00 =>00",
                                        "modified_user_id" => "1",
                                        "modified_by_name" => "Administrator",
                                        "created_by" => "1",
                                        "created_by_name" => "Administrator",
                                        "description" => "",
                                        "deleted" => "0",
                                        "created_by_link" => "",
                                        "modified_user_link" => "",
                                        "assigned_user_id" => "seed_chris_id",
                                        "assigned_user_name" => "Chris Olliver",
                                        "assigned_user_link" => "",
                                        "SecurityGroups" => "",
                                        "account_type" => "Customer",
                                        "industry" => "Insurance",
                                        "annual_revenue" => "",
                                        "phone_fax" => "",
                                        "billing_address_street" => "321 University Ave.",
                                        "billing_address_street_2" => "",
                                        "billing_address_street_3" => "",
                                        "billing_address_street_4" => "",
                                        "billing_address_city" => "Cupertino",
                                        "billing_address_state" => "--allNY",
                                        "billing_address_postalcode" => "15186",
                                        "billing_address_country" => "USA",
                                        "rating" => "",
                                        "phone_office" => "(621) 865-5301",
                                        "phone_alternate" => "",
                                        "website" => "www.beansinfo.name",
                                        "ownership" => "",
                                        "employees" => "",
                                        "ticker_symbol" => "",
                                        "shipping_address_street" => "321 University Ave.",
                                        "shipping_address_street_2" => "",
                                        "shipping_address_street_3" => "",
                                        "shipping_address_street_4" => "",
                                        "shipping_address_city" => "Cupertino",
                                        "shipping_address_state" => "NY",
                                        "shipping_address_postalcode" => "15186",
                                        "shipping_address_country" => "USA",
                                        "email1" => "kid.hr.beans@example.com",
                                        "email_addresses_primary" => "",
                                        "email_addresses" => "",
                                        "email_addresses_non_primary" => "",
                                        "parent_id" => "",
                                        "sic_code" => "",
                                        "parent_name" => "",
                                        "members" => "",
                                        "member_of" => {},
                                        "email_opt_out" => "0",
                                        "invalid_email" => "0",
                                        "cases" => "",
                                        "email" => "",
                                        "tasks" => "",
                                        "notes" => "",
                                        "meetings" => "",
                                        "calls" => "",
                                        "emails" => "",
                                        "documents" => "",
                                        "bugs" => "",
                                        "contacts" => "",
                                        "opportunities" => "",
                                        "project" => "",
                                        "leads" => "",
                                        "campaigns" => "",
                                        "campaign_accounts" => {},
                                        "campaign_id" => "",
                                        "campaign_name" => "",
                                        "prospect_lists" => "",
                                        "aos_quotes" => "",
                                        "aos_invoices" => "",
                                        "aos_contracts" => "",
                                        "jjwg_maps_lng_c" => "",
                                        "jjwg_maps_lat_c" => "",
                                        "jjwg_maps_address_c" => "",
                                        "jjwg_maps_geocode_status_c" => "",
                                        '],
                        'relationships' => ['AOS_Contracts' => [],
                                            'AOS_Invoices' => [],
                                            'AOS_Quotes' => [],
                                            'Accounts' => [],
                                            'Bugs' => [],
                                            'Calls' => [],
                                            'CampaignLog' => [],
                                            'Cases' => [],
                                            'Contacts' => [],
                                            'EmailAddress' => [],
                                            'Emails' => [],
                                            'Leads' => [],
                                            'Meetings' => [],
                                            'Notes' => [],
                                            'Opportunities' => [],
                                            'Project' => [],
                                            'ProspectLists' => [],
                                            'SecurityGroups' => [],
                                            'Tasks' => [],
                                            'Users' => [],
                                        ],
                    ],
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
                'endPoint' => '/Api/V8/module/Accounts/?filter[email1][eq]=kid.hr.beans@example.com',
            ],
        ];
    }
}
