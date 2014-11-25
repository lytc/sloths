<?php

namespace Sloths\Validation\Validator;

use Sloths\Translation\TranslatorInterface;
use Sloths\Misc\StringUtils;

trait ValidatorTrait
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'is invalid';
    /**
     * @var array
     */
    protected $messageTemplates = [
    ];

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $dataForMessage = [];

    /**
     * @param string $code
     * @return $this
     */
    public function setErrorCode($code)
    {
        $this->errorCode = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getMessageTemplate($code = null)
    {
        if (null === $code) {
            $code = $this->getErrorCode();
        }

        if (isset($this->messageTemplates[$code])) {
            return $this->messageTemplates[$code];
        }

        return $this->defaultMessageTemplate;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return $this->dataForMessage;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $template = $this->getMessageTemplate();

        if (!$template) {
            return '';
        }

        $data = $this->getDataForMessage();

        if ($this->translator) {
            return $this->translator->translate($template, $data);
        }

        return StringUtils::format($template, $data);
    }
}