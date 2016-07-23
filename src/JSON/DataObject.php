<?php
namespace Api\JSON;

use Api\Exception\ApiException;
use stdClass;

/**
 * JSON Data Object
 * Class DataObject
 *
 * @package Mapper\JSON
 */
class DataObject
{
    /**
     * @var stdClass
     */
    private $data;

    /**
     * DataObject constructor.
     *
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Create object $json from string $json
     * @param string $json
     * @return DataObject
     */
    public static function createFromString(string $json): self
    {
        return new self(json_decode($json));
    }

    /**
     * Get raw decoded json object
     * @return mixed
     */
    public function getRootObject()
    {
        return $this->data;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function has($property): bool
    {
        return property_exists($this->data, $property);
    }

    /**
     * @param string          $property
     * @param string|array    $types      List of expected types (@see gettype() function)
     * @param string|callable $constraint Regexp (preg_match) or a callable (should return error message or false)
     * @return mixed
     * @throws ApiException
     */
    public function get(string $property, $types = null, $constraint = null)
    {
        if (false === isset($this->data->$property)) {
            $this->throwException(sprintf('Property does not exist: %s', $property));
        }

        $value = $this->data->$property;

        if (null !== $types && false === in_array(gettype($value), (array)$types)) {
            $this->throwException(
                sprintf(
                    'Property %s is of type %s, expected type(s): %s',
                    $property,
                    gettype($value),
                    implode(', ', (array)$types)
                )
            );
        }

        if (null !== $constraint) {
            if (is_callable($constraint)) {
                if (false !== $error = $constraint($value)) {
                    $this->throwException(sprintf('Property %s is invalid: %s', $property, $error));
                }
            } elseif (0 === preg_match($constraint, $value)) {
                $this->throwException(sprintf('Property %s does not match %s', $property, $constraint));
            }
        }

        return $value;
    }

    /**
     * Get string
     * @param string          $property
     * @param string|callable $constraint
     * @return string
     * @throws ApiException
     */
    public function getString(string $property, $constraint = null): string
    {
        return $this->get($property, 'string', $constraint);
    }

    /**
     * Get boolean
     * @param string          $property
     * @param string|callable $constraint
     * @return string
     * @throws ApiException
     */
    public function getBoolean(string $property): bool
    {
        return $this->get($property, 'boolean');
    }

    /**
     * Get email
     * @param string $property
     * @return string
     * @throws ApiException
     */
    public function getEmail(string $property): string
    {
        $email = $this->getString($property);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        $this->throwException(sprintf('Not a valid email: %s', $email));
    }

    /**
     * @param string $message
     * @throws ApiException
     */
    private function throwException(string $message)
    {
        throw new ApiException($message, ApiException::VALIDATION);
    }

    /**
     * @param string $type
     * @param string $property
     * @return array
     * @throws ApiException
     */
    public function getArrayOf(string $type, string $property): array
    {
        $values = $this->get($property, 'array');
        foreach ($values as $value) {
            if (!(gettype($value) === $type)) {
                $this->throwException(sprintf('%s must be an array of %s', $property, $type));
            }
        }
        return $values;
    }
}
