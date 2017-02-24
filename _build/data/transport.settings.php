<?php

$settings = array();

$tmp = array(

    'api_url'   => array(
        'value' => 'http://acelle.site.ru/public/api/v1/',
        'xtype' => 'textfield',
        'area'  => 'modacelle_main',
    ),
    'api_token' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modacelle_main',
    ),
    'show_log'   => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modacelle_main',
    ),

    'default_from_email'         => array(
        'value' => 'from_email@mail.com',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_from_name'          => array(
        'value' => 'from_name',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_company'    => array(
        'value' => 'contact.company',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_address_1'  => array(
        'value' => 'contact.address_1',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_state'      => array(
        'value' => 'contact.state',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_country_id' => array(
        'value' => '169',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_city'       => array(
        'value' => 'contact.city',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_zip'        => array(
        'value' => 'contact.zip',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_phone'      => array(
        'value' => 'contact.phone',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_contact_email'      => array(
        'value' => 'contact@email.com',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_user_group_id'      => array(
        'value' => '1',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_timezone'           => array(
        'value' => 'Europe/Moscow',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),
    'default_language_id'        => array(
        'value' => '3',
        'xtype' => 'textfield',
        'area'  => 'modacelle_default',
    ),

    'default_subscribe_confirmation'   => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'modacelle_default',
    ),
    'default_unsubscribe_notification' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'modacelle_default',
    ),
    'default_send_welcome_email'       => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'modacelle_default',
    ),


    'list_name_user_site' => array(
        'value' => 'Пользователь сайта',
        'xtype' => 'textfield',
        'area'  => 'modacelle_list',
    ),
    'list_name_user_pay_order' => array(
        'value' => 'Покупатель сайта',
        'xtype' => 'textfield',
        'area'  => 'modacelle_list',
    ),


    //временные
   /* 'assets_path'              => array(
        'value' => '{base_path}modacelle/assets/components/modacelle/',
        'xtype' => 'textfield',
        'area'  => 'modacelle_temp',
    ),
    'assets_url'               => array(
        'value' => '/modacelle/assets/components/modacelle/',
        'xtype' => 'textfield',
        'area'  => 'modacelle_temp',
    ),
    'core_path'                => array(
        'value' => '{base_path}modacelle/core/components/modacelle/',
        'xtype' => 'textfield',
        'area'  => 'modacelle_temp',
    )*/
    
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'modacelle_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
