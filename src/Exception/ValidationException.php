<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/14/15
 * Time: 7:18 PM
 */

namespace Api\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends ApiException
{
    /**
     * @var ConstraintViolationList
     */
    private $violations;

    /**
     * @param ConstraintViolationList $violations
     */
    public function __construct(ConstraintViolationList $violations)
    {
        parent::__construct('Validation error', ApiException::VALIDATION, null, 401);
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationList
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
