<?php

namespace MennenOnline\LaravelResponseModels\Tests\Feature\Lexoffice\Contact;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use MennenOnline\LaravelResponseModels\Models\BaseModel;
use MennenOnline\LaravelResponseModels\Tests\BaseTest;

class PersonTest extends BaseTest
{
    use WithFaker;

    protected function setUp(): void {
        parent::setUp();

        Http::fake([
            '*' => Http::response('{
  "version": 0,
  "roles": {
    "customer": {
        "number": 12345
    },
    "vendor": {
        "number": 67890    
    }
  },
  "person": {
     "salutation": "Frau",
     "firstName": "Inge",
     "lastName": "Musterfrau"
  },
  "note": "Notizen"
}')
        ]);
    }

    public function testLexofficeContactPersonModel() {
        $response = Http::get('https://api.lexoffice.io/v1/contacts/'.$this->faker->uuid);

        $personContact = new PersonContact($response->object());

        $this->assertSame(0, $personContact->version);

        $this->assertSame(67890, $personContact->vendor_number);

        $this->assertSame(12345, $personContact->customer_number);

        $this->assertSame('Frau', $personContact->person['salutation']);

        $this->assertSame('Inge', $personContact->person['first_name']);

        $this->assertSame('Musterfrau', $personContact->person['last_name']);

        $this->assertSame('Notizen', $personContact->note);
    }
}

class PersonContact extends BaseModel
{
    protected array $fieldMap = [
        'version',
        'roles.customer.number' => 'customer_number',
        'roles.vendor.number' => 'vendor_number',
        'person',
        'note'
    ];
}