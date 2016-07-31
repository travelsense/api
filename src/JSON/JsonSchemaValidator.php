<?php

namespace Api\JSON;

use Api\Exception\ApiException;
use JsonSchema\RefResolver;
use JsonSchema\Validator;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use stdClass;


class JsonSchemaValidator
{

    /**
     * @var Validator $validator
     */
    private $validator;

    /**
     * @var string $path_to_schema_folder
     */
    private $path_to_schema_folder;

    /**
     * @var string $validate_user_schema
     */
    private $validate_user_schema = 'validate_user_schema.json';

    /**
     * Validator constructor.
     *
     * @param Validator $validator
     * @param string $path_to_schema_folder
     */
    public function __construct(Validator $validator, string $path_to_schema_folder)
    {
        $this->validator = $validator;
        $this->path_to_schema_folder = $path_to_schema_folder;
    }

    /**
     * @param stdClass $json
     * @return bool
     * @throws ApiException
     */
    public function validateUser(stdClass $json): bool
    {
        $refResolver = new RefResolver(new UriRetriever(), new UriResolver());
        $schema = $refResolver->resolve('file://'. realpath(__DIR__ . $this->path_to_schema_folder. $this->validate_user_schema));
        $this->validator->check($json, $schema);
        if($this->validator->isValid())
            return true;
        else {
            $message = "JSON does not validate. Violations:\n";
            foreach ($this->validator->getErrors() as $error) {
                $message = $message . sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            throw new ApiException(
                $message,
                ApiException::VALIDATION
            );
        }

    }
}