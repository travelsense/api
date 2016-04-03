<?php
namespace Api\JSON;
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
        return isset($this->data->$property);
    }

    /**
     * @param $property
     * @param string|array $types List of expected types (@see gettype() function)
     * @param string|callable $constraint Regexp (preg_match) or a callable (should return error message or false)
     * @return mixed
     * @throws FormatException
     */
    public function get(string $property, $types = null, $constraint = null)
    {
        if (false === isset($this->data->$property)) {
            throw new FormatException(sprintf('Property does not exist: %s', $property));
        }

        $value = $this->data->$property;

        if (null !== $types && false === in_array(gettype($value), (array) $types)) {
            throw new FormatException(
                sprintf(
                    'Property %s is of type %s, expected type(s): %s',
                    $property,
                    gettype($value),
                    implode(', ', (array) $types)
                )
            );
        }

        if (null !== $constraint) {
            if (is_callable($constraint)) {
                if (false !== $error = $constraint($value)) {
                    throw new FormatException(sprintf('Property %s is invalid: %s', $property, $error));
                }
            } elseif (0 === preg_match($constraint, $value)) {
                throw new FormatException(sprintf('Property %s does not match %s', $property, $constraint));
            }
        }

        return $value;
    }

    /**
     * Get string
     * @param string $property
     * @param string|callable $constraint
     * @return string
     * @throws FormatException
     */
    public function getString(string $property, $constraint = null)
    {
        return $this->get($property, 'string', $constraint);
    }

    /**
     * Get email
     * @param string $property
     * @return string
     */
    public function getEmail(string $property): string 
    {
        $email = $this->getString($property);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        throw new FormatException(sprintf('Not a valid email: %s', $email));
    }
}
