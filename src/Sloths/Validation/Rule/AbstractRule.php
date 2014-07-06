<?php

namespace Sloths\Validation\Rule;

use Sloths\Translation\Translator;
use Sloths\Util\StringUtils;
use Sloths\Validation\ValidatableInterface;
use Sloths\Validation\Validator;

abstract class AbstractRule implements ValidatableInterface
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var
     */
    protected $messageTemplateKey;

    /**
     * @var string
     */
    protected $message;

    /**
     * @return mixed
     */
    public function getName()
    {
        if (!$this->name) {
            $this->name = StringUtils::getClassNameWithoutNamespaceName(get_called_class());
        }

        return $this->name;
    }

    public function getMessageTemplateKey()
    {
        return $this->messageTemplateKey;
    }

    /**
     * @param Translator $translator
     * @return $this
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->translator = Validator::getDefaultTranslator();
        }

        return $this->translator;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return [];
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $message = $this->message;

        if ($message) {
            if ($message instanceof \Closure) {
                return call_user_func($message->bindTo($this), $this);
            }
            return $this->message;
        }

        return $this->getTranslator()->translate($this->getName(), $this->getDataForMessage(), $this->getMessageTemplateKey());
    }
}