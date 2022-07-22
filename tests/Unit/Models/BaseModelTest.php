<?php

namespace MennenOnline\LaravelResponseModels\Tests\Unit\Models;

use MennenOnline\LaravelResponseModels\Models\BaseModel;
use MennenOnline\LaravelResponseModels\Tests\BaseTest;

class BaseModelTest extends BaseTest
{
    public function testBaseModelConstructAndSetWithoutNestedFields() {
        $model = new class extends BaseModel {
        };

        $data = [
            'myResponseField' => 'TestData',
            'nonExistingField' => 'MissingData'
        ];

        $model->fill($data);

        $this->assertSame('TestData', $model->my_response_field);

        $this->assertCount(2, $model->getAttributes());

        $model = new class extends BaseModel {
        };

        $model->my_response_field = 'TestData';

        $this->assertSame('TestData', $model->my_response_field);
    }

    public function testBaseModelConstructAndSetWithNestedFields() {
        $model = new class extends BaseModel {
        };

        $data = [
            'myResponseField' => [
                'myNestedField' => 'TestData'
            ],
            'nonExistingField' => 'MissingData'
        ];

        $model->fill($data);

        $this->assertSame('TestData', $model->my_nested_field);

        $this->assertSame('MissingData', $model->non_existing_field);

        $this->assertCount(2, $model->getAttributes());

        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myResponseField' => 'my_test_field'
            ];
        };

        $model->my_test_field = 'TestData';

        $this->assertSame('TestData', $model->my_test_field);
    }

    public function testBaseModelWithCustomGetterAndSetter() {
        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myLowercaseResponse' => 'my_uppercase_field',
                'myUppercaseResponse' => 'my_lowercase_field'
            ];

            public function setMyUppercaseFieldAttribute($value) {
                $this->attributes['my_uppercase_field'] = str($value)->upper()->toString();
            }

            public function setMyLowercaseFieldAttribute($value) {
                $this->attributes['my_lowercase_field'] = str($value)->lower()->toString();
            }
        };

        $data = [
            'myUppercaseResponse' => 'my_lowercase_content',
            'myLowercaseResponse' => 'MY_UPPERCASE_CONTENT'
        ];

        $model->fill($data);

        $this->assertSame('MY_UPPERCASE_CONTENT', $model->my_lowercase_response);

        $this->assertSame('my_lowercase_content', $model->my_uppercase_response);

        $model = new class extends BaseModel {
            public function getMyUppercaseField($value) {
                $this->attributes['my_uppercase_field'] = str($value)->upper();
            }

            public function getMyLowercaseField($value) {
                $this->attributes['my_lowercase_field'] = str($value)->lower();
            }
        };

        $model->fill($data);

        $this->assertSame('MY_UPPERCASE_CONTENT', $model->my_lowercase_response);

        $this->assertSame('my_lowercase_content', $model->my_uppercase_response);
    }

    public function testResponseModelCanHaveFieldsWithoutADefinedResponseField() {
        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'id',
                'name',
                'mappedField' => 'mapped_field'
            ];
        };

        $data = [
            'id' => 1,
            'name' => 'some string',
            'mappedField' => 'some mapped data'
        ];

        $model->fill($data);

        $this->assertSame(1, $model->id);

        $this->assertSame('some string', $model->name);

        $this->assertSame('some mapped data', $model->mapped_field);
    }
}
