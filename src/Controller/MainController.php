<?php

namespace App\Controller;

use App\Core\FamaCore;
use App\Core\FamaReason;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Exception;

class MainController extends AbstractController
{
    /**
     * MainController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $content
     * @param boolean $assoc
     * @return mixed|null
     * @throws Exception
     */
    protected function jsonValidate(string $content, $assoc = false)
    {
        $json = !empty($content) ? (json_decode($content, $assoc) ?? null) : null;
        if (is_null($json)) {
            throw new Exception(FamaCore::translate(FamaReason::$reasonTexts[FamaReason::JSON_IS_NOT_VALID]));
        }

        return $json;
    }

    /**
     * @param array $form
     * @param Constraint $constraint
     * @return bool|array
     * @throws Exception
     */
    protected function formValidate(array $form, Constraint $constraint)
    {
        if (!is_array($form)) {
            throw new Exception(FamaCore::translate(FamaReason::$reasonTexts[FamaReason::INPUT_PARAM_IS_NOT_VALID]));
        }
        if (!$constraint instanceof Constraint) {
            throw new Exception(FamaCore::translate(FamaReason::$reasonTexts[FamaReason::INPUT_PARAM_NOT_FOUND]));
        }

        $errors = [];
        $validator = Validation::createValidator();
        $violations = $validator->validate($form, $constraint);
        if ($violations->count() > 0) {
            /**
             * @var ConstraintViolation $violation
             */
            foreach ($violations as $violation) {
                $path = explode('][', $violation->getPropertyPath());
                $path = str_replace('[', '', $path[count($path) - 1]);
                $path = str_replace(']', '', $path);
                $errors['fm'][] = [
                    'id' => $path,
                    'message' => FamaCore::translate($violation->getMessage())
                ];
            }
        }

        return $violations->count() > 0 ? $errors : true;
    }
}
