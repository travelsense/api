<?php
namespace JSON;

/**
 * JSON Data Object
 * Class DataObject
 * @package Mapper\JSON
 */
class DataObject
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * DataObject constructor.
     * @param string $json
     */
    public function __construct($json)
    {
        $this->data = json_decode($json, false);
    }

    /**
     * @param $property
     * @param string|array $types List of expected types (@see gettype() function)
     * @param string|callable $constraint Regexp (for preg_match) or a callable (bool: true if valid)
     * @param mixed $default Default value
     * @return mixed
     * @throws FormatException
     */
    public function get($property, $types = null, $constraint = null, $default = null)
    {
        if (false === isset($this->data-> $property)) {
            if (null !== $default) {
                return $default;
            }
            throw new FormatException(sprintf('Property not exists: %s', $property));
        }

        $value = $this->data->$property;

        if (null !== $types && false === in_array(gettype($value), (array) $types)) {
            throw new FormatException(sprintf(
                'Property %s has type %s, expected types: %s',
                $property,
                gettype($property),
                implode($types)
            ));
        }

        if (null !== $constraint) {
            if (is_callable($constraint) && true !== $error = $constraint($value)) {
                throw new FormatException(sprintf('Property %s is invalid: %s', $property, $error));
            } elseif (false === preg_match($constraint, $value)) {
                throw new FormatException(sprintf('Property %s does not match %s', $property, $constraint));
            }
        }

        return $value;
    }
}