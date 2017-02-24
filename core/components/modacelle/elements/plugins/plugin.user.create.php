<?php

/** @var $scriptProperties */
/** @var modacelle $modacelle */
if (!$modacelle = $modx->getService('modacelle')) {
    return;
}
$modacelle->initialize($modx->context->key);

/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnUserSave':

        $mode = $modx->getOption('mode', $scriptProperties);
        if ($mode != modSystemEvent::MODE_NEW) {
            return;
        }

        /** @var modUser $user */
        if (
            !$user = $modx->getOption('user', $scriptProperties)
            OR
            !$userPls = $modacelle->getAcelleUserData($user)
            OR
            !$listName = $modacelle->getOption('list_name_user_site', null)
            OR
            !$listUid = $modacelle->getAcelleListUidByName($listName)

        ) {
            return;
        }

        $data = array_merge($userPls, array(
            'list_uid' => $listUid,
        ));

        $modacelle->createAcelleSubscriber($data);

        break;
}