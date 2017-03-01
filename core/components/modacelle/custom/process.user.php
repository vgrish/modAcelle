<?php

if (!$modacelle = $modx->getService('modacelle')) {
    return;
}
$modacelle->initialize($modx->context->key);


$listUid = '';


$config = &$_SESSION['Console'];
$exclude = $modx->getOption('exclude', $config, array());

$q = $modx->newQuery('modUser', array('active' => 1));
if (!empty($exclude)) {
    $q->where(array('id:NOT IN' => $exclude));
}
if ($user = $modx->getObject('modUser', $q)) {
    $exclude[] = $user->get('id');
    $config['exclude'] = $exclude;

    $userPls = $modacelle->getAcelleUserData($user);
    $data = array_merge($userPls, array(
        'list_uid' => $listUid,
    ));

    // print_r($data);
    $modacelle->createAcelleSubscriber($data);
    $config['completed'] = false;

} else {

    $config['completed'] = true;
    $config['exclude'] = null;

    print_r('completed');
}
