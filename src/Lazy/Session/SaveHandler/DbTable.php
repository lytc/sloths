<?php

namespace Lazy\Session\SaveHandler;

class DbTable implements \SessionHandlerInterface
{

    /**
     * PHP >= 5.4.0<br/>
     * Close the session
     * @link http://php.net/manual/en/sessionhandlerinterafce.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function close()
    {
        // TODO: Implement close() method.
    }

    /**
     * PHP >= 5.4.0<br/>
     * Destroy a session
     * @link http://php.net/manual/en/sessionhandlerinterafce.destroy.php
     * @param int $sessionId The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function destroy($sessionId)
    {
        // TODO: Implement destroy() method.
    }

    /**
     * PHP >= 5.4.0<br/>
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterafce.gc.php
     * @param int $maxlifetime <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function gc($maxlifetime)
    {
        // TODO: Implement gc() method.
    }

    /**
     * PHP >= 5.4.0<br/>
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterafce.open.php
     * @param string $savePath The path data to store/retrieve the session.
     * @param string $sessionId The session id.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function open($savePath, $sessionId)
    {
        // TODO: Implement open() method.
    }

    /**
     * PHP >= 5.4.0<br/>
     * Read session data
     * @link http://php.net/manual/en/sessionhandlerinterafce.read.php
     * @param string $sessionId The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function read($sessionId)
    {
        // TODO: Implement read() method.
    }

    /**
     * PHP >= 5.4.0<br/>
     * Write session data
     * @link http://php.net/manual/en/sessionhandlerinterafce.write.php
     * @param string $sessionId The session id.
     * @param string $sessionData <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     */
    public function write($sessionId, $sessionData)
    {
        // TODO: Implement write() method.
    }
}