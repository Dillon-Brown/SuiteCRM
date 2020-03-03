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
                    0 => [
                        'type' => 'Account',
                        'id' => '1ac9f004-e246-96f6-4e65-5e569f7fd593',
                        'attributes' => ["name": "JBC Banking Inc",
                                        "date_entered": "2020-02-26T16:38:00+00:00",
                                        "date_modified": "2020-02-26T16:38:00+00:00",
                                        "modified_user_id": "1",
                                        "modified_by_name": "Administrator",
                                        "created_by": "1",
                                        "created_by_name": "Administrator",
                                        "description": "",
                                        "deleted": "0",
                                        "created_by_link": "",
                                        "modified_user_link": "",
                                        "assigned_user_id": "seed_chris_id",
                                        "assigned_user_name": "Chris Olliver",
                                        "assigned_user_link": "",
                                        "SecurityGroups": "",
                                        "account_type": "Customer",
                                        "industry": "Communications",
                                        "annual_revenue": "",
                                        "phone_fax": "",
                                        "billing_address_street": "111 Silicon Valley Road",
                                        "billing_address_street_2": "",
                                        "billing_address_street_3": "",
                                        "billing_address_street_4": "",
                                        "billing_address_city": "Alabama",
                                        "billing_address_state": "NY",
                                        "billing_address_postalcode": "28113",
                                        "billing_address_country": "USA",
                                        "rating": "",
                                        "phone_office": "(117) 832-3989",
                                        "phone_alternate": "",
                                        "website": "www.sugarkid.com",
                                        "ownership": "",
                                        "employees": "",
                                        "ticker_symbol": "",
                                        "shipping_address_street": "111 Silicon Valley Road",
                                        "shipping_address_street_2": "",
                                        "shipping_address_street_3": "",
                                        "shipping_address_street_4": "",
                                        "shipping_address_city": "Alabama",
                                        "shipping_address_state": "NY",
                                        "shipping_address_postalcode": "28113",
                                        "shipping_address_country": "USA",
                                        "email1": "phone.section.beans@example.name",
                                        "email_addresses_primary": "",
                                        "email_addresses": "",
                                        "email_addresses_non_primary": "",
                                        "parent_id": "",
                                        "sic_code": "",
                                        "parent_name": "",
                                        "members": "",
                                        "member_of": {},
                                        "email_opt_out": "0",
                                        "invalid_email": "0",
                                        "cases": "",
                                        "email": "",
                                        "tasks": "",
                                        "notes": "",
                                        "meetings": "",
                                        "calls": "",
                                        "emails": "",
                                        "documents": "",
                                        "bugs": "",
                                        "contacts": "",
                                        "opportunities": "",
                                        "project": "",
                                        "leads": "",
                                        "campaigns": "",
                                        "campaign_accounts": {},
                                        "campaign_id": "",
                                        "campaign_name": "",
                                        "prospect_lists": "",
                                        "aos_quotes": "",
                                        "aos_invoices": "",
                                        "aos_contracts": "",
                                        "jjwg_maps_lng_c": "",
                                        "jjwg_maps_lat_c": "",
                                        "jjwg_maps_address_c": "",
                                        "jjwg_maps_geocode_status_c": ""
                                        ],
                        'relationships' => ["AOS_Contracts": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/aos_contracts"
                                                }
                                            },
                                            "AOS_Invoices": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/aos_invoices"
                                                }
                                            },
                                            "AOS_Quotes": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/aos_quotes"
                                                }
                                            },
                                            "Accounts": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/member_of"
                                                }
                                            },
                                            "Bugs": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/bugs"
                                                }
                                            },
                                            "Calls": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/calls"
                                                }
                                            },
                                            "CampaignLog": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/campaigns"
                                                }
                                            },
                                            "Cases": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/cases"
                                                }
                                            },
                                            "Contacts": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/contacts"
                                                }
                                            },
                                            "EmailAddress": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/email_addresses"
                                                }
                                            },
                                            "Emails": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/emails"
                                                }
                                            },
                                            "Leads": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/leads"
                                                }
                                            },
                                            "Meetings": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/meetings"
                                                }
                                            },
                                            "Notes": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/notes"
                                                }
                                            },
                                            "Opportunities": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/opportunities"
                                                }
                                            },
                                            "Project": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/project"
                                                }
                                            },
                                            "ProspectLists": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/prospect_lists"
                                                }
                                            },
                                            "SecurityGroups": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/SecurityGroups"
                                                }
                                            },
                                            "Tasks": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/tasks"
                                                }
                                            },
                                            "Users": {
                                                "links": {
                                                    "related": "V8/module/Accounts/1ac9f004-e246-96f6-4e65-5e569f7fd593/relationships/modified_user_link"
                                                }
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
                'endPoint' => '/Api/V8/module/Accounts',
            ],
        ];
    }
}
