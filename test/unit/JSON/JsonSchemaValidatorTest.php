<?php

namespace Api\JSON;

use PHPUnit_Framework_TestCase;

class JsonSchemaValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidateUser()
    {
        $validator = new \JsonSchema\Validator();

        $path_to_schema_folder = '/../../app/json-schema/';

        $json_schema_validator = new \Api\JSON\JsonSchemaValidator($validator, $path_to_schema_folder);

        $json = [
            "email" => "email_example@site-name.domain-name",
                "password" => "abc123$%^",
                "firstName" => "John",
                "lastName" => "Doe",
                "picture" => "some-protocol://site_example.com/file_name.some_extension"
        ];


        $json_object = (object) $json;

        $this->assertTrue($json_schema_validator->validateUser($json_object));
    }
}