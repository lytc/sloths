<?php

namespace Sloths\Translation;

use Sloths\Misc\StringUtils;

class Translator implements TranslatorInterface
{
    /**
     * @var
     */
    protected $directory;

    /**
     * @var string
     */
    protected $locale = 'en';

    /**
     * @var string
     */
    protected $textDomain = 'messages';

    /**
     * @var string
     */
    protected $fallbackLocale = 'en';

    /**
     * @var static
     */
    protected $fallbackTranslator;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $textDomainTranslators = [];

    /**
     * @param $name
     * @return static
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->textDomainTranslators)) {
            return $this->textDomainTranslators[$name];
        }

        $translator = new static();
        $translator->setDirectory($this->directory)
            ->setTextDomain($name)
            ->setLocale($this->locale)
            ->setFallbackLocale($this->fallbackLocale);

        $this->textDomainTranslators[$name] = $translator;

        return $translator;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setFallbackLocale($locale)
    {
        $this->fallbackLocale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string $textDomain
     * @return $this
     */
    public function setTextDomain($textDomain)
    {
        $this->textDomain = $textDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getTextDomain()
    {
        return $this->textDomain;
    }

    /**
     * @return $this
     * @throws \RuntimeException
     * @throws \LogicException
     */
    protected function load()
    {
        if ($this->loaded) {
            return $this;
        }

        $file = sprintf('%s/%s/%s.php', $this->getDirectory(), $this->getTextDomain(), $this->getLocale());

        if (!file_exists($file)) {
            throw new \RuntimeException(sprintf('Message file not found: %s', $file));
        }

        $messages = require $file;
        if (!is_array($messages)) {
            throw new \LogicException(sprintf('Message file should return an array, %s given', gettype($messages)));
        }

        $this->messages = $messages;

        $this->loaded = true;
        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasMessage($key)
    {
        $this->load();
        return array_key_exists($key, $this->messages);
    }

    /**
     * @param string $key
     * @param int $number
     * @return string
     */
    public function getMessage($key, $number = null)
    {
        if ($this->hasMessage($key)) {
            $message = $this->messages[$key];

            if (is_array($message)) {
                if (is_numeric($number)) {
                    if (count($message) == 2 && isset($message[0]) && isset($message[1])) {
                        $message = $number > 1? $message[1] : $message[0];
                    } else {
                        foreach ($message as $condition => $m) {
                            if (is_numeric($condition)) {
                                if ($number == $condition) {
                                    $message = $m;
                                    break;
                                }
                            } else {
                                $parts = explode('..', $condition, 2);
                                $min = $parts[0] !== ''? $parts[0] : 0;
                                $max = isset($parts[1]) && $parts[1]? $parts[1] : PHP_INT_MAX;

                                if ($min <= $number && $number <= $max) {
                                    $message = $m;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if (is_array($message)) {
                if (isset($message[$number])) {
                    $message = $message[$number];
                } else {
                    $message = reset($message);
                }
            }

            return $message;
        }

        if (!$this->fallbackTranslator &&
            ($fallbackLocale = $this->getFallbackLocale()) && $fallbackLocale != $this->getLocale()) {
            $this->fallbackTranslator = new static();
            $this->fallbackTranslator->setDirectory($this->getDirectory())
                ->setLocale($fallbackLocale);
        }

        if ($this->fallbackTranslator) {
            return $this->fallbackTranslator->getMessage($key);
        }

        return $key;
    }

    /**
     * @param string $key
     * @param array $params
     * @param int $number
     * @return string
     */
    public function translate($key, $params = [], $number = null)
    {
        if (!is_array($params) && !$number) {
            $number = $params;
            $params = null;
        }

        $message = $this->getMessage($key, $number);
        if ($params) {
            $message = StringUtils::format($message, $params);
        }

        return $message;
    }
}