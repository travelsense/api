<?php

namespace Api\JSON;

use Api\Exception\ApiException;
use PHPUnit_Framework_TestCase;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;

class JsonSchemaValidatorTest extends PHPUnit_Framework_TestCase
{

    private $validator;

    private $path_to_schema_folder;

    private $json_schema_validator;


    public function setUp()
    {
        $this->validator = new \JsonSchema\Validator();

        $this->path_to_schema_folder = '/../../app/json-schema/';

        $ref_resolver = new \JsonSchema\RefResolver(new \JsonSchema\Uri\UriRetriever(), new \JsonSchema\Uri\UriResolver());
        
        $this->json_schema_validator = new \Api\JSON\Validator($this->validator, $this->path_to_schema_folder, $ref_resolver);
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
     * @expectedExceptionMessage Invalid JSON:
    [email] The property email is required
    [password] The property password is required
    [firstName] The property firstName is required
    [lastName] The property lastName is required
    [] The property something_wrong_properties is not defined and the definition does not allow additional properties
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
