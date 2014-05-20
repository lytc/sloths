<?php

namespace Sloths\View\Helper;

class MailTo extends Tag
{
    protected function buildAddress($addresses, $escape = false)
    {
        is_array($addresses) || ($addresses = [$addresses]);

        $result = [];
        foreach ($addresses as $address => $name) {
            if (!$name) {
                $result[] = $address;
            } else if (is_numeric($address)) {
                $result[] = $name;
            } else {
                $result[] = sprintf('%s(%s)', $escape? $this->view->escapeUrl($name) : $name, $address);
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

        $href = 'mailto:' . $this->buildAddress($addresses, true);

        if ($options) {
            $params = [];

            if (isset($options['cc'])) {
                $params['cc'] = $this->buildAddress($options['cc']);
            }

            if (isset($options['bcc'])) {
                $params['bcc'] = $this->buildAddress($options['bcc']);
            }

            if (isset($options['subject'])) {
                $params['subject'] = $options['subject'];
            }

            if (isset($options['body'])) {
                $params['body'] = $options['body'];
            }

            $href .= '?' . http_build_query($params, null, '&', PHP_QUERY_RFC3986);
        }

        return $this->tag('a', ['href' => $href])->setChildren($this->view->escape($this->buildAddress($addresses)));
    }
}