<?php
/**
 * @author dev2fun (darkfriend)
 * @copyright (c) 2025, darkfriend
 * @version 1.0.0
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

if (class_exists('Dev2funFramesModule')) return;

class Dev2funFramesModule
{
    const MODULE_ID = 'dev2fun.frames';

    public static function onPageStartEvent()
    {
        static::init();
    }

    /**
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function init()
    {
        global $APPLICATION;

        if (strpos($APPLICATION->GetCurPage(), '/bitrix/admin') !== false) {
            return;
        }

        $enabled = \Bitrix\Main\Config\Option::get(self::MODULE_ID, 'enable', 'N');
        if ($enabled === 'N') {
            return;
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $hosts = self::getHosts();
            $refHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

            if (preg_match("#({$hosts}webvisor\.com|metri[ck]a\.yandex\.(com|ru|by|com\.tr))#", $refHost)) {
                define('BX_SECURITY_SKIP_FRAMECHECK', true);
            }
        }
    }

    /**
     * @return string
     */
    public static function getHosts(): string
    {
        $hosts = trim(\Bitrix\Main\Config\Option::get(self::MODULE_ID, 'hosts', ''));
        if ($hosts) {
            $hosts = explode("\n", $hosts);
            $preparedHosts = [];
            foreach ($hosts as $host) {
                $host = trim($host);
                if ($host) {
                    $host = parse_url($host, PHP_URL_HOST);
                    $host = str_replace('.', '\.', $host);
                    if ($host) {
                        $preparedHosts[] = $host;
                    }
                }
            }
            if ($preparedHosts) {
                $hosts = implode("|", $preparedHosts) . '|';
            }
        }

        return (string)$hosts;
    }
}