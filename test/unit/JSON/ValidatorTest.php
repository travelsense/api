<?php

namespace Api\JSON;

use Api\Exception\ApiException;
use PHPUnit_Framework_TestCase;

class JsonSchemaValidatorTest extends PHPUnit_Framework_TestCase
{

    private $validator;

    private $path_to_schema_folder;

    private $json_schema_validator;


    public function setUp()
    {
        $this->validator = new \JsonSchema\Validator();

        $this->path_to_schema_folder = '/../../app/json-schema/';

        $this->json_schema_validator = new \Api\JSON\Validator($this->validator, $this->path_to_schema_folder);

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

        $this->json_schema_validator->validateUser($json_object);

        $this->assertTrue(!$this->validator->getErrors());
    }

    /**
     * @expectedException \Api\Exception\ApiException
     */
    public function testValidateUserException()
    {
        $json = [
            "something_wrong_properties" => "something_wrong_values",
        ];

        $json_object = (object) $json;

        $this->json_schema_validator->validateUser($json_object);
    }

}
