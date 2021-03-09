<?php

namespace frictionlessdata\datapackage\Validators;

use frictionlessdata\datapackage\Registry;
use frictionlessdata\datapackage\Factory;

/**
 * validate a resource descriptor
 * checks the profile attribute to determine which schema to validate with.
 */
class ResourceValidator extends BaseValidator
{
    protected function getSchemaValidationErrorClass()
    {
        return 'frictionlessdata\\datapackage\\Validators\\ResourceValidationError';
    }

    protected function getValidationProfile()
    {
        return Registry::getResourceValidationProfile($this->descriptor);
    }

    protected function getDescriptorForValidation()
    {
        $descriptor = clone $this->descriptor;

        return $descriptor;
    }

    protected function getValidationErrorMessage(array $error)
    {
        $property = $error['property'];
        // silly hack to only show properties within the resource of the fake datapackage
        // $property = str_replace("resources[0].", "", $property);
        return sprintf('[%s] %s', $property, $error['message']);
    }

    protected function getResourceClass()
    {
        return Factory::getResourceClass($this->descriptor);
    }

    protected function validateKeys()
    {
        $resourceClass = $this->getResourceClass();
        // DS: path can be a string or an array
        $sources = is_array($this->descriptor->path) ? $this->descriptor->path : array($this->descriptor->path);
        foreach ($sources as $dataSource) {
            foreach ($resourceClass::validateDataSource($dataSource, $this->basePath) as $error) {
                $this->errors[] = $error;
            }
        }
    }

    protected function getJsonSchemaFileFromRegistry($profile)
    {
        if ($filename = Registry::getJsonSchemaFile($profile)) {
            return $filename;
        } else {
            return parent::getJsonSchemaFileFromRegistry($profile);
        }
    }
}
