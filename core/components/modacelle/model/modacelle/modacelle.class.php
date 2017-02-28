<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

/**
 * The base class for modacelle.
 */
class modacelle
{
    /* @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'modacelle';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /** @var gl $gl */
    public $gl;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modacelle/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modacelle/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modacelle/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'actionUrl'       => $assetsUrl . 'action.php',
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,
        ), $config);

        $this->modx->addPackage('modacelle', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modacelle:default');
        $this->namespace = $this->getOption('namespace', $config, 'modacelle');

        $level = $modx->getLogLevel();
        $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

        /** @var gl $gl */
        if ($this->gl = $modx->getService('gl', 'gl',
            $modx->getOption('gl_core_path', null, $modx->getOption('core_path') . 'components/gl/') . 'model/gl/')
        ) {
            if (!($this->gl instanceof gl)) {
                $this->gl = false;
            }
        }

        $modx->setLogLevel($level);

    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param string $ctx
     * @param array  $scriptProperties
     *
     * @return bool|mixed
     */
    public function initialize($ctx = 'web', array $scriptProperties = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->modx->error->reset();
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if ($ctx != 'mgr' AND (!defined('MODX_API_MODE') OR !MODX_API_MODE)) {

        }

        $load = true;
        $this->initialized[$ctx] = $load;

        return $load;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '', $separator = '_')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . $separator);
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }

    /**
     * @param array  $array
     * @param array  $search
     * @param string $key
     *
     * @return mixed|null
     */
    public function getArrayValue($array = array(), $search = array(), $key = 'value')
    {
        if (empty($array) OR empty($search)) {
            return null;
        }

        foreach ($array as $row) {
            if (is_array($row) AND array_intersect_assoc($row, $search)) {
                return isset($row[$key]) ? $row[$key] : null;
            }
        }

        return null;
    }


    /**
     * @param string $message
     * @param bool   $showLog
     */
    public function log($message = '', $showLog = false)
    {
        if ($this->getOption('show_log', null, $showLog, true)) {
            if (!empty($message)) {
                $target = $this->modx->getLogTarget();
                $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($message, 1), $target, __CLASS__);
            }
        }
    }

    /**
     * @param modUser $user
     *
     * @return array
     */
    public function getAcelleUserData(modUser $user)
    {
        $data = array(
            'FIRST_NAME' => $user->get('username')
        );

        $pls = $user->toArray();
        if ($profile = $user->getOne('Profile')) {
            $data['EMAIL'] = $profile->get('email');
            $pls = array_merge($pls, $profile->toArray());
        }

        foreach ($pls as $k => $v) {
            if (!is_array($v)) {
                $data[$k] = $v;
            } else {
                $data = array_merge($data, $this->flattenArray(array($k => $v)));
            }
        }

        /* process gl opts */
        $glOpts = $this->getGlOpts();
        if (!empty($glOpts)) {
            $data = array_merge($data, $this->flattenArray(array('gl' => $glOpts)));
        }

        $this->log($data);

        return $data;
    }

    public function getGlOpts()
    {
        $data = array();

        if ($this->gl) {
            $this->gl->initialize($this->modx->context->key);
            $data = (array)$this->gl->opts;
        }

        return $data;
    }

    /**
     * @param string $mode
     *
     * @return mixed|null|string
     */
    public function getApiUrl($mode = '')
    {
        $url = $this->getOption('api_url', null, 'http://acelle.site.ru/public/api/v1/');
        $url .= $mode;

        return $url;
    }

    /**
     * @return mixed|null
     */
    public function getApiToken()
    {
        $token = $this->getOption('api_token', null, '123456');

        return $token;
    }

    /**
     * @param string $root
     * @param array  $parts
     * @param array  $data
     *
     * @return array|bool|string
     */
    public function getMode($root = '', array $parts = array(), array &$data = array())
    {
        if (empty($root)) {
            $this->log("The 'root' can not be empty ", true);

            return false;
        }

        $mode = array($root);
        foreach ($parts as $k => $v) {
            $tmp = $this->modx->getOption($k, $data);
            if (empty($tmp)) {
                $this->log("The '{$k}' can not be empty ", true);

                return false;
            }
            $mode[] = $tmp;
            if ($v) {
                $mode[] = $v;
            }
            unset($data[$k]);
        }
        $mode = $this->cleanAndImplode($mode, '/');

        return $mode;
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleLists(array $data = array())
    {
        $mode = $this->getMode('lists', array(), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleListInfo(array $data = array())
    {
        $mode = $this->getMode('lists', array('uid' => false), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function createAcelleList(array $data = array())
    {
        $mode = $this->getMode('lists', array(), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(
            'name'                     => null,
            'from_email'               => $this->getOption('default_from_email', null),
            'from_name'                => $this->getOption('default_from_name', null),
            'default_subject'          => null,
            'contact[company]'         => $this->getOption('default_contact_company', null),
            'contact[state]'           => $this->getOption('default_contact_state', null),
            'contact[address_1]'       => $this->getOption('default_contact_address_1', null),
            'contact[city]'            => $this->getOption('default_contact_city', null),
            'contact[zip]'             => $this->getOption('default_contact_zip', null),
            'contact[phone]'           => $this->getOption('default_contact_phone', null),
            'contact[country_id]'      => $this->getOption('default_contact_country_id', null),
            'contact[email]'           => $this->getOption('default_contact_email', null),
            'contact[url]'             => null,
            'subscribe_confirmation'   => $this->getOption('default_subscribe_confirmation', null, true),
            'send_welcome_email'       => $this->getOption('default_send_welcome_email', null, true),
            'unsubscribe_notification' => $this->getOption('default_unsubscribe_notification', null, true),
        ), $data);

        return $this->request($mode, $data, 'POST');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleСampaigns(array $data = array())
    {
        $mode = $this->getMode('campaigns', array(), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleСampaignInfo(array $data = array())
    {
        $mode = $this->getMode('campaigns', array('uid' => false), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleSubscribers(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers'), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(
            'per_page' => 20
        ), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function getAcelleSubscriberInfo(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers', 'uid' => false), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'GET');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function createAcelleSubscriber(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers/store'), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(
            'EMAIL'      => null,
            'FIRST_NAME' => null,
            'LAST_NAME'  => null,
        ), $data);

        return $this->request($mode, $data, 'POST');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function subscribeAcelleSubscriber(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers', 'uid' => 'subscribe'), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'PATCH');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function unsubscribeAcelleSubscriber(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers', 'uid' => 'unsubscribe'), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'PATCH');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function deleteAcelleSubscriber(array $data = array())
    {
        $mode = $this->getMode('lists', array('list_uid' => 'subscribers', 'uid' => 'delete'), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(), $data);

        return $this->request($mode, $data, 'DELETE');
    }


    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function createAcelleUser(array $data = array())
    {
        $mode = $this->getMode('users', array(), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(
            'user_group_id' => $this->getOption('default_user_group_id', null),
            'email'         => null,
            'first_name'    => null,
            'last_name'     => null,
            'timezone'      => $this->getOption('default_timezone', null),
            'language_id'   => $this->getOption('default_language_id', null),
            'password'      => null,
        ), $data);

        return $this->request($mode, $data, 'POST');
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public function updateAcelleUser(array $data = array())
    {
        $mode = $this->getMode('users', array('uid' => false), $data);
        if (!$mode) {
            return $mode;
        }
        $data = array_merge(array(
            'user_group_id' => $this->getOption('default_user_group_id', null),
            'email'         => null,
            'first_name'    => null,
            'last_name'     => null,
            'timezone'      => $this->getOption('default_timezone', null),
            'language_id'   => $this->getOption('default_language_id', null),
        ), $data);

        return $this->request($mode, $data, 'POST');
    }


    /**
     * @param string $name
     *
     * @return bool|mixed|null
     */
    public function getAcelleListUidByName($name = '')
    {
        $row = $this->getAcelleLists();
        if (!is_array($row)) {
            return false;
        }

        return $this->getArrayValue($row, array('name' => $name), 'uid');
    }

    /**
     * @param string $email
     * @param        $list
     *
     * @return bool|mixed|null
     */
    public function getAcelleSubscriberUidByEmail($email = '', $list)
    {
        $row = $this->getAcelleSubscribers(array('list_uid' => $list));
        if (!is_array($row)) {
            return false;
        }

        return $this->getArrayValue($row, array('email' => $email), 'uid');
    }


    /**
     * @param string $mode
     * @param array  $data
     * @param string $method
     * @param string $url
     *
     * @return array|mixed
     */
    public function request($mode = '', $data = array(), $method = 'GET', $url = '')
    {
        $mode = trim($mode, '/');
        if (empty($url)) {
            $url = $this->getApiUrl($mode);
        }

        $data = array_merge(array('api_token' => $this->getApiToken()), $data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json'
        ));

        $method = strtoupper($method);
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, count($data));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
        }

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL            => $url,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => 0
            )
        );

        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if (in_array($code, array('401', '500'))) {
            $this->log($data, true);
            $data = array();
        } else {
            $data = json_decode($data, true);
        }
        $this->log($data);

        return $data;
    }


}