<?php
namespace Sloths\Translation;

interface TranslatorInterface
{
    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale);

    /**
     * @param string $textDomain
     * @return $this
     */
    public function setTextDomain($textDomain);

    /**
     * @param string $key
     * @param array $params
     * @param int $number
     * @return string
     */
    public function translate($key, $params = [], $number = null);
}