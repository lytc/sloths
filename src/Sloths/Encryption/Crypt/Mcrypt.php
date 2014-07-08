<?php

namespace Sloths\Encryption\Crypt;

use Sloths\Util\StringUtils;

if (!extension_loaded('mcrypt')) {
    throw new \RuntimeException(
        sprintf('%s\Mcrypt require the Mcrypt extension', __NAMESPACE__)
    );
}

class Mcrypt implements CryptInterface
{
    const ALGO_CAS_128            = 'cast-128';
    const ALGO_GOST               = 'gost';
    const ALGO_RIJNDAEL_128       = 'rijndael-128';
    const ALGO_TWOFISH            = 'twofish';
    const ALGO_ARCFOUR            = 'arcfour';
    const ALGO_CAST_256           = 'cast-256';
    const ALGO_LOKI97             = 'loki97';
    const ALGO_RIJNDAEL_192       = 'rijndael-192';
    const ALGO_SAFERPLUS          = 'saferplus';
    const ALGO_WAKE               = 'wake';
    const ALGO_BLOWFISH_COMPAT    = 'blowfish-compat';
    const ALGO_DES                = 'des';
    const ALGO_RIJNDAEL_256       = 'rijndael-256';
    const ALGO_SERPENT            = 'serpent';
    const ALGO_XTEA               = 'xtea';
    const ALGO_BLOWFISH           = 'blowfish';
    const ALGO_ENIGMA             = 'enigma';
    const ALGO_RC2                = 'rc2';
    const ALGO_TRIPLEDES          = 'tripledes';

    const MODE_CBC      = 'cbc';
    const MODE_CFB      = 'cfb';
    const MODE_CTR      = 'ctr';
    const MODE_ECB      = 'ecb';
    const MODE_NCFB     = 'ncfb';
    const MODE_NOFB     = 'nofb';
    const MODE_OFB      = 'ofb';
    const MODE_STREAM   = 'stream';

    const DEFAULT_ALGO = self::ALGO_RIJNDAEL_128;
    const DEFAULT_MODE = self::MODE_CBC;

    /**
     * @var
     */
    protected static $listAlgorithms;

    /**
     * @var
     */
    protected static $listModes;

    /**
     * @var
     */
    protected static $defaultAlgorithm;

    /**
     * @var
     */
    protected static $defaultMode;

    /**
     * @var
     */
    protected static $defaultKey;

    /**
     * @var
     */
    protected static $defaultIv;

    /**
     * @var Mcrypt
     */
    protected static $instance;

    /**
     * @return Mcrypt
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array
     */
    public static function listAlgorithms()
    {
        if (!self::$listAlgorithms) {
            $algorithms = mcrypt_list_algorithms();
            self::$listAlgorithms = array_combine($algorithms, $algorithms);
        }

        return self::$listAlgorithms;
    }

    /**
     * @return array
     */
    public static function listModes()
    {
        if (!self::$listModes) {
            $listModes = mcrypt_list_modes();
            self::$listModes = array_combine($listModes, $listModes);
        }

        return self::$listModes;
    }

    /**
     * @param $algorithm
     * @return bool
     */
    public static function isSupportedAlgorithm($algorithm)
    {
        return isset(self::listAlgorithms()[$algorithm]);
    }

    /**
     * @param $mode
     * @return bool
     */
    public static function isSupportedMode($mode)
    {
        return isset(self::listModes()[$mode]);
    }

    public static function setDefaultAlgorithm($algorithm)
    {
        if (!self::isSupportedAlgorithm($algorithm)) {
            throw new \InvalidArgumentException(sprintf('The algorithm %s is not supported', $algorithm));
        }
        self::$defaultAlgorithm = $algorithm;
    }

    public static function getDefaultAlgorithm()
    {
        if (!self::$defaultAlgorithm) {
            self::setDefaultAlgorithm(self::DEFAULT_ALGO);
        }

        return self::$defaultAlgorithm;
    }

    public static function setDefaultMode($mode)
    {
        if (!self::isSupportedMode($mode)) {
            throw new \InvalidArgumentException(sprintf('The mode %s is not supported', $mode));
        }

        self::$defaultMode = $mode;
    }

    public static function getDefaultMode()
    {
        if (!self::$defaultMode) {
            self::setDefaultMode(self::DEFAULT_MODE);
        }

        return self::$defaultMode;
    }

    /**
     * @param $key
     */
    public static function setDefaultKey($key)
    {
        self::$defaultKey = $key;
    }

    /**
     * @return mixed
     */
    public static function getDefaultKey()
    {
        return self::$defaultKey;
    }

    /**
     * @param $iv
     */
    public static function setDefaultIv($iv)
    {
        self::$defaultIv = $iv;
    }

    /**
     * @return mixed
     */
    public static function getDefaultIv()
    {
        return self::$defaultIv;
    }

    /**
     * @param $size
     * @return string
     */
    public static function createRandomKey($size)
    {
        if ($size instanceof self) {
            $size = $size->getMaxKeySize();
        }

        return StringUtils::random($size, StringUtils::RANDOM_ALL);
    }

    /**
     * @param $size
     * @return string
     */
    public static function createRandomIv($size)
    {
        if ($size instanceof self) {
            $size = $size->getIvSize();
        }

        return mcrypt_create_iv($size, MCRYPT_RAND);
    }

    /**
     * @var
     */
    protected $algorithm;
    /**
     * @var
     */
    protected $mode;
    /**
     * @var
     */
    protected $key;
    /**
     * @var
     */
    protected $iv;
    /**
     * @var bool
     */
    protected $base64Mode = true;

    /**
     * @param null $key
     * @param null $iv
     * @param string $algorithm
     * @param string $mode
     */
    public function __construct($key = null, $iv = null, $algorithm = null, $mode = null)
    {
        if ($algorithm) {
            $this->setAlgorithm($algorithm);
        }
        if ($mode) {
            $this->setMode($mode);
        }

        if ($key) {
            $this->setKey($key);
        }

        if ($iv) {
            $this->setIv($iv);
        }
    }

    /**
     * @param $algorithm
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setAlgorithm($algorithm)
    {
        if (!self::isSupportedAlgorithm($algorithm)) {
            throw new \InvalidArgumentException(sprintf('The algorithm %s is not supported', $algorithm));
        }

        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlgorithm()
    {
        if (!$this->algorithm) {
            $this->setAlgorithm(self::getDefaultAlgorithm());
        }

        return $this->algorithm;
    }

    /**
     * @param $mode
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setMode($mode)
    {
        if (!self::isSupportedMode($mode)) {
            throw new \InvalidArgumentException(sprintf('The mode %s is not supported', $mode));
        }

        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        if (!$this->mode) {
            $this->setMode(self::getDefaultMode());
        }
        return $this->mode;
    }

    /**
     * @return array
     */
    public function getSupportedKeySizes()
    {
        return mcrypt_module_get_supported_key_sizes($this->getAlgorithm());
    }

    /**
     * @return int
     */
    public function getMaxKeySize()
    {
        return mcrypt_module_get_algo_key_size($this->getAlgorithm());
    }

    /**
     * @param $key
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setKey($key)
    {
        $keyLength = strlen($key);
        $supportedKeySizes = $this->getSupportedKeySizes();
        $maxKeySize = $this->getMaxKeySize();

        if ($supportedKeySizes) {
            if (!in_array($keyLength, $supportedKeySizes)) {
                throw new \InvalidArgumentException(
                    sprintf('Key size must be in [%s], %s given', implode(', ', $supportedKeySizes), $keyLength)
                );
            }
        } elseif ($keyLength > $maxKeySize) {
            throw new \InvalidArgumentException(
                sprintf('Key size must be between 1 and %s, %s given', $maxKeySize, $keyLength)
            );
        }

        $this->key = $key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        if (!$this->key) {
            $this->setKey(self::getDefaultKey());
        }

        return $this->key;
    }

    /**
     * @return int
     */
    public function getIvSize()
    {
        return mcrypt_get_iv_size($this->getAlgorithm(), $this->getMode());
    }

    /**
     * @param $iv
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setIv($iv)
    {
        $ivLength = strlen($iv);
        $ivSize = $this->getIvSize();

        if ($ivLength != $ivSize) {
            throw new \InvalidArgumentException(
                sprintf('IV size must be %s, %s given', $ivSize, $ivLength)
            );
        }

        $this->iv = $iv;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIv()
    {
        if (!$this->iv) {
            $this->setIv(self::getDefaultIv());
        }

        return $this->iv;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function setBase64Mode($state = true)
    {
        $this->base64Mode = !!$state;
        return $this;
    }

    /**
     * @return bool
     */
    public function getBase64Mode()
    {
        return $this->base64Mode;
    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $iv = $this->getIv();
        $encrypted = mcrypt_encrypt($this->getAlgorithm(), $this->getKey(), $data, $this->getMode(), $iv);
        $encrypted = $iv . $encrypted;

        if ($this->getBase64Mode()) {
            $encrypted = base64_encode($encrypted);
        }

        return $encrypted;
    }

    /**
     * @param string $encryptedData
     * @return string
     * @throws \InvalidArgumentException
     */
    public function decrypt($encryptedData)
    {
        if ($this->getBase64Mode()) {
            $encryptedData = base64_decode($encryptedData, true);
        }

        if (!$encryptedData) {
            throw new \InvalidArgumentException('Invalid encrypted data');
        }

        $ivSize = $this->getIvSize();
        $iv = substr($encryptedData, 0, $ivSize);
        $cypherText = substr($encryptedData, $ivSize);

        $result = rtrim(mcrypt_decrypt($this->getAlgorithm(), $this->getKey(), $cypherText, $this->getMode(), $iv), "\0");
        return $result;
    }
}