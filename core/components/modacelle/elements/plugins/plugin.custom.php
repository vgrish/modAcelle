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
            !$items = $order->getMany('Products')
        ) {
            return;
        }

        /** @var msOrderProduct $item */
        foreach ($items as $item) {
            /** @var msProduct $product */
            if (!$product = $item->getOne('Product')) {
                continue;
            }
            $listName = $product->get('pagetitle');


            $q = $modx->newQuery('modTemplateVar');
            $q->leftJoin('modTemplateVarResource', 'modTemplateVarResource',
                'modTemplateVarResource.tmplvarid = modTemplateVar.id');
            $q->where(array(
                'modTemplateVar.name'              => "adrbook_unisender",
                'modTemplateVarResource.contentid' => $product->get('id')
            ));
            $q->select('modTemplateVarResource.value');
            if ($q->prepare() AND $q->stmt->execute()) {
                $tmp = (string)$modx->getValue($q->stmt);
                if (!empty($tmp)) {
                    $listName = $tmp;
                }
            }

            $listUid = $modacelle->getAcelleListUidByName($listName);
            if (!$listUid) {
                $response = $modacelle->createAcelleList(array('name' => $listName));
                $listUid = $modx->getOption('list_uid', $response);
            }

            if (!$listUid) {
                continue;
            }

            $data = array();
            $email = $modx->getOption('EMAIL', $userPls);

            switch (true) {
                case $status == 1 AND empty($order->get('cost')):
                case $status == 2:

                    $userUid = $modacelle->getAcelleSubscriberUidByEmail($email, $listUid);
                    /* user is exist */
                    if ($userUid) {
                        $modacelle->subscribeAcelleSubscriber(array('list_uid' => $listUid, 'uid' => $userUid));
                    } else {
                        $data = array_merge($userPls, array(
                            'list_uid' => $listUid,
                        ));
                        $modacelle->createAcelleSubscriber($data);
                    }

                    break;
                case $status == 4:

                    $userUid = $modacelle->getAcelleSubscriberUidByEmail($email, $listUid);
                    /* user is exist */
                    if ($userUid) {
                        $modacelle->unsubscribeAcelleSubscriber(array('list_uid' => $listUid, 'uid' => $userUid));
                    }

                    break;

                default:
                    break;
            }
        }

        break;
}