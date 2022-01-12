<?php



namespace OpenStack\Common\JsonSchema;

use JsonSchema\Validator;

class Schema
{
    /** @var object */
    private $body;

    /** @var Validator */
    private $validator;

    public function __construct($body, Validator $validator = null)
    {
        $this->body      = (object) $body;
        $this->validator = $validator ?: new Validator();
    }

    public function getPropertyPaths()
    {
        $paths = [];

        foreach ($this->body->properties as $propertyName => $property) {
            $paths[] = sprintf('/%s', $propertyName);
        }

        return $paths;
    }

    public function normalizeObject($subject, array $aliases)
    {
        $out = new \stdClass();

        foreach ($this->body->properties as $propertyName => $property) {
            $name = $aliases[$propertyName] ?? $propertyName;

            if (isset($property->readOnly) && true === $property->readOnly) {
                continue;
            } elseif (property_exists($subject, $name)) {
                $out->$propertyName = $subject->$name;
            } elseif (property_exists($subject, $propertyName)) {
                $out->$propertyName = $subject->$propertyName;
            }
        }

        return $out;
    }

    public function validate($data)
    {
        $this->validator->check($data, $this->body);
    }

    public function isValid()
    {
        return $this->validator->isValid();
    }

    public function getErrors()
    {
        return $this->validator->getErrors();
    }

    public function getErrorString()
    {
        $msg = "Provided values do not validate. Errors:\n";

        foreach ($this->getErrors() as $error) {
            $msg .= sprintf("[%s] %s\n", $error['property'], $error['message']);
        }

        return $msg;
    }
}
