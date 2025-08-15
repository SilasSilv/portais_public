<?php
namespace api\v1\vendor\utils;

class UrlUtils
{
    public static function getUrl($type='BASE') {
        if ($type == 'BASE') {
            return sprintf(
                "%s://%s:%s/",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']
            );
        } elseif ($type = 'ALL') {
            return sprintf(
                "%s://%s:%s%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI']
            );
        }        
    }
}