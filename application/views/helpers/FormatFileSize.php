<?php

namespace Application\View\Helper;
use Sloths\View\Helper\AbstractHelper;

class FormatFileSize extends AbstractHelper
{
    public function formatFileSize($bytes)
    {
        if ($bytes >= 1000000000) {
            return round(($bytes / 1000000000), 2) . ' GB';
        }

        if ($bytes >= 1000000) {
            return round(($bytes / 1000000), 2) . ' MB';
        }

        return round(($bytes / 1000), 2) . ' KB';
    }
}