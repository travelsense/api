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
     * @param string $property
     * @return bool
     */
    public function has($property)
    {
        return isset($this->data->$property);
    }

    /**
     * @param $property
     * @param string|array $types List of expected types (@see gettype() function)
     * @param string|callable $constraint Regexp (preg_match) or a callable (should return error message or false)
     * @return mixed
     * @throws FormatException
     */
    public function get($property, $types = null, $constraint = null)
    {
        if (false === isset($this->data->$property)) {
            throw new FormatException(sprintf('Property does not exist: %s', $property));
        }

        $value = $this->data->$property;

        if (null !== $types && false === in_array(gettype($value), (array) $types)) {
            throw new FormatException(sprintf(
                'Property %s is a %s, expected: %s',
                $property,
                gettype($property),
                implode(', ', (array) $types)
            ));
        }

        if (null !== $constraint) {
            if (is_callable($constraint)) {
                if(false !== $error = $constraint($value)) {
                    throw new FormatException(sprintf('Property %s is invalid: %s', $property, $error));
                }
            } elseif (0 === preg_match($constraint, $value)) {
                throw new FormatException(sprintf('Property %s does not match %s', $property, $constraint));
            }
        }

        return $value;
    }

    /**
     * @param string $property
     * @param string|callable $constraint
     * @return string
     * @throws FormatException
     */
    public function getString($property, $constraint = null)
    {
        return $this->get($property, 'string', $constraint);
    }

}