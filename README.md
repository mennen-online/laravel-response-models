# Laravel Httpclient Response Model

This package offers a Base Model, which can be extended with Data Models to map HTTP Responses.

It is not only bound / limited to Laravels Http Client, so you can use ist with any JSON Response you want.

# Installation

You can install the package via composer:

```shell
composer require mennen-online/laravel-response-models
```

# Usage

Here's a little example how to create Response Models with this Package:

```php
use MennenOnline\Models\BaseModel;

class PersonContact extends BaseModel {
    protected array $fieldMap = [
        'version' => 'version',
        'roles.customer.number' => 'customer_number',
        'roles.vendor.number' => 'vendor_number',
        'person' => 'person',
        'note' => 'note'    
    ];   
}
```

Now you have defined the Response Model for PersonContact

[Example Lexoffice Contact Response used for this](https://developers.lexoffice.io/docs/#contacts-endpoint-create-a-contact)

Now we make a Request to Lexoffice API: https://api.lexoffice.io/v1/contacts/2a730f45-8078-3ccc-a2ed-563f18208eff

We can use the Response to create the ResponseModel:

```php
 use Illuminate\Support\Facades\Http;

$response = Http::get('https://api.lexoffice.io/v1/contacts/2a730f45-8078-3ccc-a2ed-563f18208eff');

$personContact = new PersonContact($response->object());
```

Now we can access the Properties on the Top Level as Properties, every other property as Array:

```php
$personContact->version // 1

$personContact->customer_number // 12345

$personContact->person['first_name'] // Verena
```

And so on.

Also nested Arrays are automatically converted to snake_case - Accessible with Arr::get for example.

# Features

Inspired of Laravel Models, it is currently possible to:

- Define getter and setter (set{AttributeName}Attribute | get{AttributeName}Attribute)

# Testing

```shell
composer test
```

# License

The MIT License (MIT).