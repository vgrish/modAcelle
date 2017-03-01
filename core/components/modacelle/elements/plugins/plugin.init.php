<?php

/** @var $scriptProperties */

switch ($modx->event->name) {

    case 'OnMODXInit':

        /* add modAcelleEventObject */
        $modx->map['modAcelleEventObject'] = array(
            'table'  => '',
            'fields' => array()
        );

        if (!class_exists('modAcelleEventObject')) {
            class modAcelleEventObject extends xPDOObject
            {
            }

            class modAcelleEventObject_mysql extends modAcelleEventObject
            {
            }
        }

        break;

    case 'modAcelleObjectProcess':

        if (
            !$type = $modx->getOption('type', $scriptProperties)
            OR
            !$object = $modx->getOption('object', $scriptProperties)
        ) {
            return;
        }

        switch ($type) {
            case 'user_data':
                //$object->set('add_field','value');
                break;
        }
        
        break;
}
