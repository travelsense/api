<?php

namespace Api\JSON;

use PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    private $validator;

    private $path_to_schema_folder;

    private $json_schema_validator;

    private $ref_resolver;

    private $schema_object;


    public function setUp()
    {
        $this->validator = new \JsonSchema\Validator();
        $this->path_to_schema_folder = __DIR__ . '/../../../app/json-schema/';
        $this->ref_resolver = $this->getMockBuilder('\\JsonSchema\\RefResolver')->disableOriginalConstructor()->setMethods(['resolve'])->getMock();
        $this->json_schema_validator = new \Api\JSON\Validator($this->validator, $this->path_to_schema_folder, $this->ref_resolver);
        $schema = [
            "id" => "http =>//json-schema.org/draft-04/schema#",
            "comment" => "Schema for user validation during registration",
            "type" => "object",
            "properties" => [
                "email" => [
                    "format" => "email"
                ],
                "picture" => [
                    "format" => "uri"
                ],
                "firstName" => [
                    "type" => "string",
                    "minLength" => 1,
                    "maxLength" => 128,
                    "pattern" => "^[a-zA-Z0-9_-]+$"
                ],
                "lastName" => [
                    "type" => "string",
                    "minLength" => 1,
                    "maxLength" => 128,
                    "pattern" => "^[a-zA-Z0-9_-]+$"
                ],
                "creator" => [
                    "type" => "boolean"
                ],
                "password" => [
                    "type" => "string",
                    "minLength" => 4,
                    "maxLength" => 256
                ]
            ],
            "required" => ["email", "password", "firstName", "lastName"],
            "additionalProperties" => false
        ];
        $this->schema_object = (object)$schema;
    }

    public function testValidateUser()
    {
        $json = [
            "email" => "email_example@site-name.domain-name",
            "password" => "abc123$%^",
            "firstName" => "John",
            "lastName" => "Doe",
            "picture" => "some-protocol://site_example.com/file_name.some_extension"
        ];

        $json_object = (object) $json;
        $this->ref_resolver->method('resolve')
            ->with('file://' . realpath($this->path_to_schema_folder . 'validate_user_schema.json'))
            ->willReturn($this->schema_object);

        $this->json_schema_validator->validateUser($json_object);
        $this->assertTrue(!$this->validator->getErrors());
    }

    public function testValidateUserException()
    {
        $json = [
            "something_wrong_properties" => "something_wrong_values",
        ];

        $json_object = (object) $json;

        $message = "Invalid JSON:\n[email] The property email is required\n[password] The property password is required\n[firstName] The property firstName is required\n[lastName] The property lastName is required\n[] The property something_wrong_properties is not defined and the definition does not allow additional properties\n";

        $this->ref_resolver->method('resolve')
            ->with('file://' . realpath($this->path_to_schema_folder . 'validate_user_schema.json'))
            ->willReturn($this->schema_object);

        try {
            $this->json_schema_validator->validateUser($json_object);
            $this->fail('No exception thrown');
        } catch (\Api\Exception\ApiException $e) {
            $this->assertEquals($message, $e->getMessage());
        }
    }
}
