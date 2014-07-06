<?php

namespace SlothsTest\Encryption\Crypt;

use Sloths\Encryption\Crypt\Mcrypt;
use Sloths\Util\StringUtils;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Encryption\Crypt\Mcrypt
 */
class McryptTest extends TestCase
{
    public function testConstructor()
    {
        $algo = Mcrypt::ALGO_DES;
        $mode = Mcrypt::MODE_ECB;
        $key = Mcrypt::createRandomKey(mcrypt_module_get_algo_key_size($algo));
        $iv = Mcrypt::createRandomIv(mcrypt_get_iv_size($algo, $mode));

        $mcrypt = new Mcrypt($key, $iv, $algo, $mode);

        $this->assertSame($algo, $mcrypt->getAlgorithm());
        $this->assertSame($mode, $mcrypt->getMode());
        $this->assertSame($key, $mcrypt->getKey());
        $this->assertSame($iv, $mcrypt->getIv());
    }

    public function testSingletonInstance()
    {
        $mcrypt = Mcrypt::getInstance();
        $mcrypt->setKey(Mcrypt::createRandomKey($mcrypt));
        $mcrypt->setIv(Mcrypt::createRandomIv($mcrypt));

        $data = 'test';
        $encrypted = $mcrypt->encrypt($data);
        $this->assertSame($data, $mcrypt->decrypt($encrypted));
    }

    public function testDefault()
    {
        $algo = Mcrypt::ALGO_DES;
        $mode = Mcrypt::MODE_ECB;

        $key = Mcrypt::createRandomKey(mcrypt_module_get_algo_key_size($algo));
        $iv = Mcrypt::createRandomIv(mcrypt_get_iv_size($algo, $mode));

        Mcrypt::setDefaultAlgorithm($algo);
        Mcrypt::setDefaultMode($mode);
        Mcrypt::setDefaultKey($key);
        Mcrypt::setDefaultIv($iv);

        $this->assertSame($algo, Mcrypt::getDefaultAlgorithm());
        $this->assertSame($mode, Mcrypt::getDefaultMode());
        $this->assertSame($key, Mcrypt::getDefaultKey());
        $this->assertSame($iv, Mcrypt::getDefaultIv());

        $mcrypt = new Mcrypt();
        $this->assertSame($algo, $mcrypt->getAlgorithm());
        $this->assertSame($mode, $mcrypt->getMode());
        $this->assertSame($key, $mcrypt->getKey());
        $this->assertSame($iv, $mcrypt->getIv());
    }

    public function testBase64Mode()
    {
        $mcrypt = new Mcrypt();
        $mcrypt->setBase64Mode(false);
        $encrypted = $mcrypt->encrypt('foo');
        $this->assertSame(base64_encode($encrypted), $mcrypt->setBase64Mode(true)->encrypt('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidDefaultAlgorithmShouldThrowAnException()
    {
        Mcrypt::setDefaultAlgorithm('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidDefaultModeShouldThrowAnException()
    {
        Mcrypt::setDefaultMode('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidAlgorithmShouldThrowAnException()
    {
        $mcrypt = new Mcrypt();
        $mcrypt->setAlgorithm('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidModeShouldThrowAnException()
    {
        $mcrypt = new Mcrypt();
        $mcrypt->setMode('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidKeyShouldThrowAnException()
    {
        $mcrypt = $this->getMock('Sloths\Encryption\Crypt\Mcrypt', ['getSupportedKeySizes']);
        $mcrypt->expects($this->once())->method('getSupportedKeySizes')->willReturn([1, 3]);
        $mcrypt->setKey('fo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidKeySizeShouldThrowExceptionIfKeySizeGreaterThanMaxKeySize()
    {
        $mcrypt = $this->getMock('Sloths\Encryption\Crypt\Mcrypt', ['getSupportedKeySizes', 'getMaxKeySize']);
        $mcrypt->expects($this->once())->method('getSupportedKeySizes')->willReturn([]);
        $mcrypt->expects($this->once())->method('getMaxKeySize')->willReturn(1);
        $mcrypt->setKey('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidIvSizeShouldThrowAnException()
    {
        $mcrypt = $this->getMock('Sloths\Encryption\Crypt\Mcrypt', ['getIvSize']);
        $mcrypt->expects($this->once())->method('getIvSize')->willReturn(1);
        $mcrypt->setIv('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDecryptInvalidDataShouldThrowAnException()
    {
        $mcrypt = new Mcrypt();
        $mcrypt->decrypt('?');
    }
}