<?php
/**
 * @author dev2fun (darkfriend)
 * @copyright (c) 2025, darkfriend
 * @version 1.0.0
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

IncludeModuleLangFile(__FILE__);

if (class_exists("dev2fun_frames")) {
    return;
}

use Bitrix\Main\ModuleManager,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\EventManager,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option;

Loader::registerAutoLoadClasses(
    "dev2fun.frames",
    [
        "Dev2funFramesModule" => 'include.php',
    ]
);

class dev2fun_frames extends CModule
{
    public $MODULE_ID = "dev2fun.frames";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion["VERSION"] ?? '1.0.0';
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"] ?? '2025-10-15 23:00:00';

        $this->MODULE_NAME = Loc::getMessage("DEV2FUN_FRAMES_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("DEV2FUN_FRAMES_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "dev2fun";
        $this->PARTNER_URI = "https://dev2fun.com";
    }

    /**
     * @return void
     */
    public function DoInstall()
    {
        global $APPLICATION;
        if (!check_bitrix_sessid()) {
            return;
        }

        try {
            $this->registerEvents();

            ModuleManager::registerModule($this->MODULE_ID);

        } catch (Exception $e) {
            $GLOBALS['DEV2FUN_FRAMES_ERROR'] = $e->getMessage();
            $GLOBALS['DEV2FUN_FRAMES_ERROR_NOTES'] = Loc::getMessage('DEV2FUN_FRAMES_ERROR_CHECK_NOFOUND_NOTES');
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage("D2F_MODULE_DRAGGABLE_STEP_ERROR"),
                __DIR__ . "/error.php"
            );
            return;
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("D2F_MODULE_DRAGGABLE_STEP_FINAL"),
            __DIR__ . "/final.php"
        );
    }

    /**
     * @return void
     */
    public function DoUninstall()
    {
        global $APPLICATION;
        if (!check_bitrix_sessid()) {
            return;
        }

        $this->unRegisterEvents();
        Option::delete($this->MODULE_ID);

        ModuleManager::unRegisterModule($this->MODULE_ID);

        CAdminMessage::ShowMessage([
            "MESSAGE" => Loc::getMessage('DEV2FUN_FRAMES_UNINSTALL_SUCCESS'),
            "TYPE" => "OK",
        ]);

        echo BeginNote();
        echo Loc::getMessage("DEV2FUN_FRAMES_UNINSTALL_LAST_MSG");
        echo EndNote();
    }

    /**
     * @return void
     */
    public function registerEvents()
    {
        EventManager::getInstance()
            ->registerEventHandler(
                "main",
                "OnPageStart",
                $this->MODULE_ID,
                "Dev2funFramesModule",
                "onPageStartEvent"
            );
    }

    /**
     * @return void
     */
    public function unRegisterEvents()
    {
        EventManager::getInstance()
            ->unRegisterEventHandler('main', 'OnPageStart', $this->MODULE_ID);
    }
}
