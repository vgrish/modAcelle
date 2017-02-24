<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var modacelle $modacelle */
$modacelle = $modx->getService('modacelle', 'modacelle', $modx->getOption('modacelle_core_path', null,
        $modx->getOption('core_path') . 'components/modacelle/') . 'model/modacelle/');
$modx->lexicon->load('modacelle:default');

// handle request
$corePath = $modx->getOption('modacelle_core_path', null, $modx->getOption('core_path') . 'components/modacelle/');
$path = $modx->getOption('processorsPath', $modacelle->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location'        => '',
));