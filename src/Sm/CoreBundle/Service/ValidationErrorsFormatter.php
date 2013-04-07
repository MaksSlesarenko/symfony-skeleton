<?php
namespace Sm\CoreBundle\Service;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorsFormatter
{
    /**
     * Constructor
     *
     * @param ConstraintViolationList $list
     */
    public function format(ConstraintViolationList $list)
    {
        $result = array();
        foreach ($list as $violation) {
            $result[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return array('validation_errors' => $result);
    }
}