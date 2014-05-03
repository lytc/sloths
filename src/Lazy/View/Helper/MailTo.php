<?php

namespace Lazy\View\Helper;

class MailTo extends AbstractHelper
{
    protected static function buildAddress($addresses)
    {
        is_array($addresses) || ($addresses = [$addresses]);

        $result = [];
        foreach ($addresses as $address => $name) {
            if (is_numeric($address)) {
                $result[] = $name;
            } else {
                $result[] = sprintf('%s(%s)', rawurlencode($name), $address);
            }
        }

        return implode(',', $result);
    }

    public function mailTo($addresses, $name = null, array $options = null)
    {
        if (is_array($name)) {
            $options = $name;
            $name = null;
        }

        is_array($addresses) || ($addresses = [$addresses => $name]);

        $result = 'mailto:' . self::buildAddress($addresses);

        if ($options) {
            $params = [];

            if (isset($options['cc'])) {
                $params[] = sprintf('cc=%s', self::buildAddress($options['cc']));
            }

            if (isset($options['bcc'])) {
                $params[] = sprintf('bcc=%s', self::buildAddress($options['bcc']));
            }

            if (isset($options['subject'])) {
                $params[] = sprintf('subject=%s', rawurlencode($options['subject']));
            }

            if (isset($options['body'])) {
                $params[] = sprintf('body=%s', rawurlencode($options['body']));
            }

            $result .= '?' . implode('&', $params);
        }

        return $result;
    }
}