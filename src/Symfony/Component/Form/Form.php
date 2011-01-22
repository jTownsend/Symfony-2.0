<?php

namespace Symfony\Component\Form;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Form\Exception\FormException;

/**
 * Form represents a form.
 *
 * A form is composed of a validator schema and a widget form schema.
 *
 * Form also takes care of Csrf protection by default.
 *
 * A Csrf secret can be any random string. If set to false, it disables the
 * Csrf protection, and if set to null, it forces the form to use the global
 * Csrf secret. If the global Csrf secret is also null, then a random one
 * is generated on the fly.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Form extends FieldGroup
{
    protected $validator = null;
    protected $validationGroups = null;

    private $csrfSecret = null;
    private $csrfFieldName = null;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array|object $data
     * @param ValidatorInterface $validator
     * @param array $options
     */
    public function __construct($name, $data = null, ValidatorInterface $validator = null, array $options = array())
    {
        $this->validator = $validator;

        // Prefill the form with the given data
        if (null !== $data) {
            $this->setData($data);
        }

        if (FormConfiguration::isDefaultCsrfProtectionEnabled()) {
            $this->enableCsrfProtection();
        }

        $this->addOption('field_factory');

        parent::__construct($name, $options);

        // If data is passed to this constructor, objects from parent forms
        // should be ignored
        if (null !== $data) {
            $this->setPropertyPath(null);
        }
    }

    /**
     * Sets the validation groups for this form.
     *
     * @param array|string $validationGroups
     */
    public function setValidationGroups($validationGroups)
    {
        $this->validationGroups = null === $validationGroups ? $validationGroups : (array) $validationGroups;
    }

    /**
     * Returns the validation groups for this form.
     *
     * @return array
     */
    public function getValidationGroups()
    {
        return $this->validationGroups;
    }

    /**
     * Returns a factory for automatically creating fields based on metadata
     * available for a form's object
     *
     * @return FieldFactoryInterface  The factory
     */
    public function getFieldFactory()
    {
        return $this->getOption('field_factory');
    }

    /**
     * Binds the form with values and files.
     *
     * This method is final because it is very easy to break a form when
     * overriding this method and adding logic that depends on $taintedFiles.
     * You should override doBind() instead where the uploaded files are
     * already merged into the data array.
     *
     * @param  array $taintedValues  The form data of the $_POST array
     * @param  array $taintedFiles   An array of uploaded files
     * @return boolean               Whether the form is valid
     */
    final public function bind($taintedValues, array $taintedFiles = null)
    {
        if (null === $taintedFiles) {
            if ($this->isMultipart() && $this->getParent() === null) {
                throw new \InvalidArgumentException('You must provide a files array for multipart forms');
            }

            $taintedFiles = array();
        }

        if (null === $taintedValues) {
            $taintedValues = array();
        }

        $this->doBind(self::deepArrayUnion($taintedValues, $taintedFiles));

        if ($this->getParent() === null) {
            if ($this->validator === null) {
                throw new FormException('A validator is required for binding. Forgot to pass it to the constructor of the form?');
            }

            if ($violations = $this->validator->validate($this, $this->getValidationGroups())) {
                // TODO: test me
                foreach ($violations as $violation) {
                    $propertyPath = new PropertyPath($violation->getPropertyPath());
                    $iterator = $propertyPath->getIterator();

                    if ($iterator->current() == 'data') {
                        $type = self::DATA_ERROR;
                        $iterator->next(); // point at the first data element
                    } else {
                        $type = self::FIELD_ERROR;
                    }

                    $this->addError(new FieldError($violation->getMessageTemplate(), $violation->getMessageParameters()), $iterator, $type);
                }
            }
        }
    }

    /**
     * Binds the form with the given data.
     *
     * @param  array $taintedData  The data to bind to the form
     * @return boolean             Whether the form is valid
     */
    protected function doBind(array $taintedData)
    {
        parent::bind($taintedData);
    }

    /**
     * Returns a CSRF token for the given CSRF secret
     *
     * If you want to change the algorithm used to compute the token, you
     * can override this method.
     *
     * @param  string $secret The secret string to use
     *
     * @return string A token string
     */
    protected function generateCsrfToken($secret)
    {
        $secret .= get_class($this);
        $defaultSecrets = FormConfiguration::getDefaultCsrfSecrets();

        foreach ($defaultSecrets as $defaultSecret) {
            if ($defaultSecret instanceof \Closure) {
                $defaultSecret = $defaultSecret();
            }

            $secret .= $defaultSecret;
        }

        return md5($secret);
    }

    /**
     * @return true if this form is CSRF protected
     */
    public function isCsrfProtected()
    {
        return $this->has($this->getCsrfFieldName());
    }

    /**
     * Enables CSRF protection for this form.
     */
    public function enableCsrfProtection($csrfFieldName = null, $csrfSecret = null)
    {
        if (!$this->isCsrfProtected()) {
            if (null === $csrfFieldName) {
                $csrfFieldName = FormConfiguration::getDefaultCsrfFieldName();
            }

            if (null === $csrfSecret) {
                $csrfSecret = md5(__FILE__.php_uname());
            }

            $field = new HiddenField($csrfFieldName, array(
                'property_path' => null,
            ));
            $field->setData($this->generateCsrfToken($csrfSecret));
            $this->add($field);

            $this->csrfFieldName = $csrfFieldName;
            $this->csrfSecret = $csrfSecret;
        }
    }

    /**
     * Disables CSRF protection for this form.
     */
    public function disableCsrfProtection()
    {
        if ($this->isCsrfProtected()) {
            $this->remove($this->getCsrfFieldName());

            $this->csrfFieldName = null;
            $this->csrfSecret = null;
        }
    }

    /**
     * Returns the CSRF field name used in this form
     *
     * @return string The CSRF field name
     */
    public function getCsrfFieldName()
    {
        return $this->csrfFieldName;
    }

    /**
     * Returns the CSRF secret used in this form
     *
     * @return string The CSRF secret
     */
    public function getCsrfSecret()
    {
        return $this->csrfSecret;
    }

    /**
     * Returns whether the CSRF token is valid
     *
     * @return boolean
     */
    public function isCsrfTokenValid()
    {
        if (!$this->isCsrfProtected()) {
            return true;
        } else {
            return $this->get($this->getCsrfFieldName())->getDisplayedData() === $this->generateCsrfToken($this->getCsrfSecret());
        }
    }

    /**
     * Returns whether the maximum POST size was reached in this request.
     *
     * @return boolean
     */
    public function isPostMaxSizeReached()
    {
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $length = (int) $_SERVER['CONTENT_LENGTH'];
            $max = trim(ini_get('post_max_size'));

            switch (strtolower(substr($max, -1))) {
                // The 'G' modifier is available since PHP 5.1.0
                case 'g':
                    $max *= 1024;
                case 'm':
                    $max *= 1024;
                case 'k':
                    $max *= 1024;
            }

            return $length > $max;
        } else {
            return false;
        }
    }

    /**
     * Merges two arrays without reindexing numeric keys.
     *
     * @param array $array1 An array to merge
     * @param array $array2 An array to merge
     *
     * @return array The merged array
     */
    static protected function deepArrayUnion($array1, $array2)
    {
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $array1[$key] = self::deepArrayUnion($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }
}
