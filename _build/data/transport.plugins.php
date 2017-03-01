<?php

$plugins = array();

$tmp = array(
    'modAcelleInit'         => array(
        'file'        => 'init',
        'description' => '',
        'events'      => array(
            'OnMODXInit'             => array('priority' => 150),
            'modAcelleObjectProcess' => array('priority' => 0),
        )
    ),
    'modAcelleUserCreate'   => array(
        'file'        => 'user.create',
        'description' => '',
        'events'      => array(
            'OnUserSave' => array('priority' => 150),
        )
    ),
    'modAcelleUserPayOrder' => array(
        'file'        => 'user.pay.order',
        'description' => '',
        'events'      => array(
            'msOnChangeOrderStatus' => array('priority' => 150),
        ),
        'disabled'    => 1
    ),
    'modAcelleCustom'       => array(
        'file'        => 'custom',
        'description' => '',
        'events'      => array(
            'msOnChangeOrderStatus' => array('priority' => 150),
        ),
        'disabled'    => 1
    )


);

foreach ($tmp as $k => $v) {
    /* @avr modplugin $plugin */
    $plugin = $modx->newObject('modPlugin');
    $plugin->fromArray(array(
        'name'        => $k,
        'category'    => 0,
        'description' => @$v['description'],
        'plugincode'  => getSnippetContent($sources['source_core'] . '/elements/plugins/plugin.' . $v['file'] . '.php'),
        'static'      => BUILD_PLUGIN_STATIC,
        'source'      => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/plugin.' . $v['file'] . '.php',
        'disabled'    => isset($v['disabled']) ? $v['disabled'] : 0
    ), '', true, true);

    $events = array();
    if (!empty($v['events'])) {
        foreach ($v['events'] as $k2 => $v2) {
            /* @var modPluginEvent $event */
            $event = $modx->newObject('modPluginEvent');
            $event->fromArray(array_merge(
                array(
                    'event'       => $k2,
                    'priority'    => 0,
                    'propertyset' => 0,
                ), $v2
            ), '', true, true);
            $events[] = $event;
        }
        unset($v['events']);
    }

    if (!empty($events)) {
        $plugin->addMany($events);
    }

    $plugins[] = $plugin;
}

unset($tmp, $properties);
return $plugins;