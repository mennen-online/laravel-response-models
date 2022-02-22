<?php

namespace MennenOnline\LaravelHttpResponseProcessor\Tests\Unit\Models;

use MennenOnline\LaravelHttpResponseProcessor\Models\BaseModel;
use MennenOnline\LaravelHttpResponseProcessor\Tests\BaseTest;

class BaseModelTest extends BaseTest
{
    public function testBaseModelConstructAndSetWithoutNestedFields() {
        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myResponseField' => 'my_test_field'
            ];
        };

        $data = [
            'myResponseField' => 'TestData',
            'nonExistingField' => 'MissingData'
        ];

        $model->fill($data);

        $this->assertSame('TestData', $model->my_test_field);

        $this->assertCount(1, $model->getAttributes());

        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myResponseField' => 'my_test_field'
            ];
        };

        $model->my_test_field = 'TestData';

        $this->assertSame('TestData', $model->my_test_field);
    }

    public function testBaseModelConstructAndSetWithNestedFields() {
        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myResponseField.myNestedField' => 'my_test_field'
            ];
        };

        $data = [
            'myResponseField' => [
                'myNestedField' => 'TestData'
            ],
            'nonExistingField' => 'MissingData'
        ];

        $model->fill($data);

        $this->assertSame('TestData', $model->my_test_field);

        $this->assertCount(1, $model->getAttributes());

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

        $this->assertSame('MY_UPPERCASE_CONTENT', $model->my_uppercase_field);

        $this->assertSame('my_lowercase_content', $model->my_lowercase_field);

        $model = new class extends BaseModel {
            protected array $fieldMap = [
                'myLowercaseResponse' => 'my_uppercase_field',
                'myUppercaseResponse' => 'my_lowercase_field'
            ];

            public function getMyUppercaseField($value) {
                $this->attributes['my_uppercase_field'] = str($value)->upper();
            }

            public function getMyLowercaseField($value) {
                $this->attributes['my_lowercase_field'] = str($value)->lower();
            }
        };

        $model->fill($data);

        $this->assertSame('MY_UPPERCASE_CONTENT', $model->my_uppercase_field);

        $this->assertSame('my_lowercase_content', $model->my_lowercase_field);
    }
}
