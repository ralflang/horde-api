<?php
class Api_SettingsExporter_Api extends Horde_Controller_SettingsExporter_Default
{
    public function exportFilters(Horde_Controller_FilterCollection $filters, Horde_Injector $injector)
    {
        $mapper = $injector->getInstance('Horde_Routes_Mapper');
        $default = array('method' => 'httpBasic', 'backend' => 'horde');
        // Handle auth filters first as they likely result in access denied
        // Assume a configured default if the route has no auth information
        $authConfig = empty($mapper->matchList[0]->defaults['auth']) ? $default : $mapper->matchList[0]->defaults['auth'];
        // Return an auth module (method and backend) based on the config as a prefilter
        //
        $perms = array();
        $post = array();
    }
}