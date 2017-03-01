<?php

echo "<pre>";
if (!$modacelle = $modx->getService('modacelle')) {
    return;
}
$modacelle->initialize($modx->context->key);

$listUid = '';
$userGroup = array();

$config = &$_SESSION['Console'];
$exclude = $modx->getOption('exclude', $config, array());

$q = $modx->newQuery('modUser', array('active' => 1));
$q->innerJoin('modUserGroupMember', 'UserGroupMembers');

if (!empty($exclude)) {
    $q->where(array('id:NOT IN' => $exclude));
}
if (!empty($userGroup)) {
    $q->where(array('UserGroupMembers.user_group:IN' => $userGroup));
}

if ($user = $modx->getObject('modUser', $q)) {
    $exclude[] = $user->get('id');
    $config['exclude'] = $exclude;

    $userPls = $modacelle->getAcelleUserData($user);
    $data = array_merge($userPls, array(
        'list_uid' => $listUid,
    ));

    //print_r($data);

    $modacelle->createAcelleSubscriber($data);
    $config['completed'] = false;

} else {

    $config['completed'] = true;
    $config['exclude'] = null;
    print_r('completed');
}
