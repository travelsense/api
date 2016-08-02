<?php

namespace Api\JSON;

use Api\Exception\ApiException;
use JsonSchema\RefResolver;
use JsonSchema\Validator as JsonSchemaValidator;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use stdClass;

class Validator
{

    /**
     * @var JsonSchemaValidator $validator
     */
    private $validator;

    /**
     * @var string $schema_path
     */
    private $schema_path;

    /**
     * @var RefResolver
     */
    private $ref_resolver;

    /**
     * Validator constructor.
     *
     * @param JsonSchemaValidator $validator
     * @param string $schema_path
     */
    public function __construct(JsonSchemaValidator $validator, string $schema_path, RefResolver $ref_resolver)
    {
        $this->validator = $validator;
        $this->schema_path = $schema_path;
        $this->ref_resolver = $ref_resolver;
    }

    /**
     * @param stdClass $json
     * @return bool
     * @throws ApiException
     */
    public function validateUser(stdClass $json)
    {
        $schema = $this->ref_resolver->resolve('file://'. realpath(__DIR__ . $this->schema_path. 'validate_user_schema.json'));
        $this->validator->check($json, $schema);
        if (!$this->validator->isValid()) {
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
