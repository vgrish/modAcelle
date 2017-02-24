<?php


/** @var $scriptProperties */
/** @var modacelle $modacelle */
if (!$modacelle = $modx->getService('modacelle')) {
    return;
}
$modacelle->initialize($modx->context->key);

/** @var modX $modx */
switch ($modx->event->name) {

    case 'msOnChangeOrderStatus':

        $status = $modx->getOption('status', $scriptProperties);
        if ($status != 2) {
            return;
        }

        /** @var msOrder $order */
        /** @var modUser $user */
        /** @var modUserProfile $profile */
        if (
            !$order = $modx->getOption('order', $scriptProperties)
            OR
            !$user = $order->getOne('User')
            OR
            !$userPls = $modacelle->getAcelleUserData($user)
            OR
            !$listName = $modacelle->getOption('list_name_user_pay_order', null)
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