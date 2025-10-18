<?php
/**
 * @author dev2fun (darkfriend)
 * @copyright (c) 2025, darkfriend
 * @version 1.0.0
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
$curModuleName = 'dev2fun.frames';

Loc::loadMessages(__FILE__);

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'ICON' => 'main_settings',
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    ],
    [
        "DIV" => "donate",
        "TAB" => Loc::getMessage('DEV2FUN_FRAMES_SEC_DONATE_TAB'),
        "ICON" => "main_user_edit",
        "TITLE" => Loc::getMessage('DEV2FUN_FRAMES_SEC_DONATE_TAB_TITLE'),
    ],
];

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if ($request->isPost() && check_bitrix_sessid()) {
    $commonOptions = $request->getPost('common_options');
    Option::set($curModuleName, 'enable', $commonOptions['enable'] ?? 'N');
    Option::set($curModuleName, 'hosts', $commonOptions['hosts'] ?? '');

    LocalRedirect($APPLICATION->GetCurPageParam('saved=1', ['saved']));
}

if ($_REQUEST['saved'] ?? false) {
    CAdminMessage::showMessage([
        "MESSAGE" => Loc::getMessage("DEV2FUN_FRAMES_REFERENCES_OPTIONS_SAVED"),
        "TYPE" => "OK",
    ]);
}

$tabControl->Begin();
?>

<link rel="stylesheet" href="https://unpkg.com/blaze@4.0.0-6/scss/dist/components.cards.min.css">
<link rel="stylesheet" href="https://unpkg.com/blaze@4.0.0-6/scss/dist/objects.grid.min.css">
<link rel="stylesheet" href="https://unpkg.com/blaze@4.0.0-6/scss/dist/objects.grid.responsive.min.css">
<link rel="stylesheet" href="https://unpkg.com/blaze@4.0.0-6/scss/dist/objects.containers.min.css">
<link rel="stylesheet" href="https://unpkg.com/blaze@4.0.0-6/scss/dist/components.tables.min.css">

<!--/bitrix/admin/settings.php?mid=dev2fun.frames&lang=ru&tabControl_active_tab=edit1-->
<form
    method="post"
    action="<?= sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s', urlencode($mid), LANGUAGE_ID) ?>&<?= $tabControl->ActiveTabParam() ?>"
    enctype="multipart/form-data"
    name="editform"
    class="editform"
>
    <?php
    echo bitrix_sessid_post();
    $tabControl->BeginNextTab();
    ?>

    <tr>
        <td width="40%">
            <label for="common_options_enable">
                <?= Loc::getMessage("DEV2FUN_FRAMES_ENABLE_LABEL") ?>:
            </label>
        </td>
        <td width="60%">
            <input
                type="checkbox"
                id="common_options_enable"
                name="common_options[enable]"
                value="Y"
                <?php
                if (Option::get($curModuleName, "enable", 'N') === 'Y') {
                    echo 'checked';
                }
                ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="common_options_hosts">
                <?= Loc::getMessage("DEV2FUN_FRAMES_HOSTS_LABEL") ?>:
            </label>
        </td>
        <td width="60%">
            <textarea
                id="common_options_hosts"
                name="common_options[hosts]"
                cols="30"
                rows="10"
            ><?php 
                echo Option::get($curModuleName, 'hosts', '');
            ?></textarea>
        </td>
    </tr>

    <?php include __DIR__.'/tabs/donate.php'?>

    <?php
    $tabControl->Buttons([
        "btnSave" => true,
        "btnApply" => true,
        "btnCancel" => true,
        "back_url" => $APPLICATION->GetCurUri(),
    ]);
    $tabControl->End();
    ?>
</form>