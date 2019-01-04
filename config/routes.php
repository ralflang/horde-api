<?php
/**
 * Setup default routes
 */
$mapper->connect('/introspection/apps',
    array(
        'controller' => 'IntrospectionApps',
        'action' => 'FOO',
        'auth' => array('method' => 'http', 'backend' => 'horde')
    ));
$mapper->connect('/introspection/apis',
    array(
        'controller' => 'IntrospectionApis',
        'action' => 'FOO'
    ));
