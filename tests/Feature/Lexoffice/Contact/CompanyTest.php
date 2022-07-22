<?php

namespace MennenOnline\LaravelResponseModels\Tests\Feature\Lexoffice\Contact;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use MennenOnline\LaravelResponseModels\Models\BaseModel;
use MennenOnline\LaravelResponseModels\Tests\BaseTest;

class CompanyTest extends BaseTest
{
    use WithFaker;

    protected function setUp(): void {
        parent::setUp();

        Http::fake(
            [
                '*' => Http::response('{
    "id": "be9475f4-ef80-442b-8ab9-3ab8b1a2aeb9",
    "organizationId": "aa93e8a8-2aa3-470b-b914-caad8a255dd8",
    "version": 1,
    "roles": {
        "customer": {
            "number": 10307
        },
        "vendor": {
            "number": 70303
        }
    },
    "company": {
        "name": "Testfirma",
        "taxNumber": "12345/12345",
        "vatRegistrationId": "DE123456789",
        "allowTaxFreeInvoices": true,
        "contactPersons": [
            {
                "salutation": "Herr",
                "firstName": "Max",
                "lastName": "Mustermann",
                "primary": true,
                "emailAddress": "contactpersonmail@lexoffice.de",
                "phoneNumber": "08000/11111"
            },
            {
                "salutation": "Frau",
                "firstName": "Ilse",
                "lastName": "Musterfrau",
                "primary": false,
                "emailAddress": "contactpersonmail1@lexoffice.de",
                "phoneNumber": "08000/11112"
            }
        ]
    },
    "addresses": {
        "billing": [
            {
                "supplement": "Rechnungsadressenzusatz",
                "street": "Hauptstr. 5",
                "zip": "12345",
                "city": "Musterort",
                "countryCode": "DE"
            }
        ],
        "shipping": [
            {
                "supplement": "Lieferadressenzusatz",
                "street": "Schulstr. 13",
                "zip": "76543",
                "city": "Musterstadt",
                "countryCode": "DE"
            }
        ]
    },
    "xRechnung": {
        "buyerReference": "04011000-1234512345-35",
        "vendorNumberAtCustomer": "70123456"
    },
    "emailAddresses": {
        "business": [
            "business@lexoffice.de"
        ],
        "office": [
            "office@lexoffice.de"
        ],
        "private": [
            "private@lexoffice.de"
        ],
        "other": [
            "other@lexoffice.de"
        ]
    },
    "phoneNumbers": {
        "business": [
            "08000/1231"
        ],
        "office": [
            "08000/1232"
        ],
        "mobile": [
            "08000/1233"
        ],
        "private": [
            "08000/1234"
        ],
        "fax": [
            "08000/1235"
        ],
        "other": [
            "08000/1236"
        ]
    },
    "note": "Notizen",
    "archived": false
}')
            ]
        );
    }

    public function testLexofficeContactCompanyModel() {
        $response = Http::get('https://api.lexoffice.io/v1/contacts/'.$this->faker->uuid);

        $companyContact = new CompanyContact((array)$response->object());

        $this->assertSame("be9475f4-ef80-442b-8ab9-3ab8b1a2aeb9", $companyContact->id);

        $this->assertSame("aa93e8a8-2aa3-470b-b914-caad8a255dd8", $companyContact->organization_id);

        $this->assertSame(1, $companyContact->version);

        $this->assertSame(10307, $companyContact->getRolesCustomerNumberAttribute());

        $this->assertSame(70303, $companyContact->getRolesVendorNumberAttribute());

        $this->assertSame([
            'name' => 'Testfirma',
            'tax_number' => "12345/12345",
            'vat_registration_id' => 'DE123456789',
            'allow_tax_free_invoices' => true,
            'contact_persons' => [
                [
                    'salutation' => 'Herr',
                    'first_name' => 'Max',
                    'last_name' => 'Mustermann',
                    'primary' => true,
                    'email_address' => 'contactpersonmail@lexoffice.de',
                    'phone_number' => '08000/11111'
                ],
                [
                    'salutation' => 'Frau',
                    'first_name' => 'Ilse',
                    'last_name' => 'Musterfrau',
                    'primary' => false,
                    'email_address' => 'contactpersonmail1@lexoffice.de',
                    'phone_number' => '08000/11112'
                ]
            ]
        ], $companyContact->company);

        $this->assertSame([
            'billing' => [
                [
                    'supplement' => 'Rechnungsadressenzusatz',
                    'street' => 'Hauptstr. 5',
                    'zip' => '12345',
                    'city' => 'Musterort',
                    'country_code' => 'DE'
                ]
            ],
            'shipping' => [
                [
                    'supplement' => 'Lieferadressenzusatz',
                    'street' => 'Schulstr. 13',
                    'zip' => '76543',
                    'city' => 'Musterstadt',
                    'country_code' => 'DE'
                ]
            ]
        ], $companyContact->addresses);

        $this->assertSame([
            'buyer_reference' => '04011000-1234512345-35',
            'vendor_number_at_customer' => '70123456'
        ], $companyContact->x_rechnung);

        $this->assertSame([
            'business' => [
                'business@lexoffice.de'
            ],
            'office' => [
                'office@lexoffice.de'
            ],
            'private' => [
                'private@lexoffice.de'
            ],
            'other' => [
                'other@lexoffice.de'
            ]
        ], $companyContact->email_addresses);

        $this->assertSame([
            'business' => [
                '08000/1231'
            ],
            'office' => [
                '08000/1232'
            ],
            'mobile' => [
                '08000/1233'
            ],
            'private' => [
                '08000/1234'
            ],
            'fax' => [
                '08000/1235'
            ],
            'other' => [
                '08000/1236'
            ]
        ], $companyContact->phone_numbers);

        $this->assertSame('Notizen', $companyContact->note);

        $this->assertFalse($companyContact->archived);
    }
}

class CompanyContact extends BaseModel {
    protected array $fieldMap = [
        'id',
        'organizationId' => 'organization_id',
        'version',
        'roles.customer.number' => 'customer_number',
        'roles.vendor.number' => 'vendor_number',
        'company.name' => 'company.name',
        'company.taxNumber' => 'company.tax_number',
        'company.vatRegistrationId' => 'company.vat_registration_id',
        'company.allowTaxFreeInvoices' => 'company.allow_tax_free_invoices',
        'company.contactPersons' => 'company.contact_persons',
        'addresses.billing' => 'addresses.billing',
        'addresses.shipping' => 'addresses.shipping',
        'xRechnung' => 'x_rechnung',
        'emailAddresses' => 'email_addresses',
        'phoneNumbers' => 'phone_numbers',
        'note',
        'archived'
    ];
}