<?php

namespace Sloths\Validation;

use Sloths\Translation\TranslatorInterface;
use Sloths\Misc\StringUtils;
use Sloths\Validation\Validator\Callback;
use Sloths\Validation\Validator\Chain;
use Sloths\Validation\Validator\ValidatorInterface;

class Validator
{
    /**
     * @var Validator\Chain[]
     */
    protected $chains = [];

    /**
     * @var Validator\ValidatorInterface[]
     */
    protected $fails = [];

    /**
     * @var bool
     */
    protected $validated = false;

    /**
     * @var \Sloths\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected static $customRules = [];

    /**
     * @param Validator\Chain[] $chains
     */
    public function __construct(array $chains = [])
    {
        $this->addChains($chains);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->chains = [];
        $this->fails = [];
        $this->validated = false;

        return $this;
    }

    /**
     * @param string $name
     * @param \Closure $callback
     * @return $this
     */
    public static function addRule($name, \Closure $callback)
    {
        self::$customRules[$name] = $callback;
    }

    /**
     * @param array $rules
     * @return $this
     */
    public static function addRules(array $rules)
    {
        foreach ($rules as $name => $callback) {
            self::addRule($name, $callback);
        }
    }

    public static function createRule($rule, array $args = [])
    {
        if (is_string($rule)) {
            # from builtin rules
            $validatorClassName = __NAMESPACE__ . '\Validator\\' . ucfirst($rule);

            if (class_exists($validatorClassName)) {
                $reflectionClass = new \ReflectionClass($validatorClassName);
                return $reflectionClass->newInstanceArgs($args);
            }
            if (isset(self::$customRules[$rule])) {
                return new Callback(self::$customRules[$rule], $args);
            }

            throw new \InvalidArgumentException('Undefined rule: ' . $rule);
        } elseif ($rule instanceof \Closure) {
            return new Callback($rule, $args);
        } elseif ($rule instanceof ValidatorInterface) {
            return $rule;
        }

        throw new \InvalidArgumentException(sprintf(
            'Rule must be an string of rule name or callback or instance of \Sloths\Validation\Validator\ValidatorInterface. %s given.',
            gettype($rule)
        ));
    }

    /**
     * @param Validator\Chain[] $chains
     * @return $this
     */
    public function addChains(array $chains)
    {
        foreach ($chains as $name => $chain) {
            $this->add($name, $chain);
        }

        return $this;
    }

    /**
     * @return Validator\Chain[]
     */
    public function getChains()
    {
        return $this->chains;
    }

    /**
     * @param string $name
     * @param Chain|string|array $chain
     * @return $this
     */
    public function add($name, $chain)
    {
        if (!$chain instanceof Chain) {
            if (!is_array($chain)) {
                $chain = [$chain];
            }
            $chain = Chain::fromArray($chain);
        }

        $this->chains[$name] = $chain;
        return $this;
    }

    /**
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param array|\ArrayAccess $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate($data)
    {
        if (!is_array($data) && !$data instanceof \ArrayAccess) {
            throw new \InvalidArgumentException(sprintf(
                'Data must be an array or an instanceof \ArrayAccess. %s given.', gettype($data)
            ));
        }

        $fails = [];

        foreach ($this->getChains() as $name => $chain) {
            $value = isset($data[$name])? $data[$name] : '';
            if (true !== ($validator = $chain->validate($value))) {
                $fails[$name] = $validator;
            }
        }

        $this->fails = $fails;
        $this->validated = true;
        return !$fails;
    }

    protected function ensureValidated()
    {
        if (!$this->validated) {
            throw new \RuntimeException('Requires to call ::validate() first');
        }
    }

    /**
     * @return Validator\ValidatorInterface[]
     */
    public function fails()
    {
        $this->ensureValidated();
        return $this->fails;
    }

    /**
     * @return array
     */
    public function getMessageTemplates()
    {
        $messageTemplates = [];

        foreach ($this->fails() as $name => $validator) {
            $messageTemplate = $validator->getMessageTemplate();
            $dataForMessage = $validator->getDataForMessage();

            $messageTemplates[$name] = [
                'template'  => $messageTemplate,
                'data'      => $dataForMessage
            ];
        }

        return $messageTemplates;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = [];
        $translator = $this->getTranslator();

        foreach ($this->getMessageTemplates() as $name => $data) {
            if ($translator) {
                $message = $translator->translate($data['template'], $data['data']);
            } else {
                $message = StringUtils::format($data['template'], $data['data']);
            }

            $messages[$name] = $message;
        }

        return $messages;
    }
}