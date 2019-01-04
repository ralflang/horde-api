<?php
/**
 * Alternative implementation of Horde_Core_Controller_RequestMapper
 */
class Api_RequestMapper
{
    /**
     * @var Horde_Routes_Mapper $mapper
     */
    protected $_mapper;

    public function __construct(Horde_Routes_Mapper $mapper)
    {
        $this->_mapper = $mapper;
    }

    /**
     * Analyze request to setup runner
     *
     * Determine which app to run, which controller to use and setup any prefilters
     */
    public function getRequestConfiguration(Horde_Injector $injector)
    {
        $request = $injector->getInstance('Horde_Controller_Request');
        $registry = $injector->getInstance('Horde_Registry');
        $settingsFinder = $injector->getInstance('Horde_Core_Controller_SettingsFinder');

        $config = $injector->createInstance('Horde_Core_Controller_RequestConfiguration');


        $uri = substr($request->getPath(), strlen($registry->get('webroot', 'api')));
        $uri = trim($uri, '/');
        if (strpos($uri, '/') === false) {
            $api = $uri;
        } else {
            list($api,) = explode('/', $uri, 2);
        }
        $app = $registry->hasInterface($api);
        if ($app) {
            // TODO: Debug log
        } else {
            // TODO: Shall we fallback to interpreting the first slug as app name?
            // TODO: Do we want any magic for catching json-rpc & friends or do we implement 
            //       a special controller to handle the fixed-url stuff?
            throw new Api_Exception("No active app implements the $api api");
        }


        $config->setApplication($app);

        // Check for route definitions.
        $fileroot = $registry->get('fileroot', $app);
        $routeFile = $fileroot . '/config/routes.php';
        if (!file_exists($routeFile)) {
            $config->setControllerName('Horde_Core_Controller_NotFound');
            return $config;
        }

        // Push $app onto the registry
        $registry->pushApp($app);

        // Application routes are relative only to the application. Let the
        // mapper know where they start.
        $this->_mapper->prefix = $registry->get('webroot', $app);

        // Set the application controller directory
        $this->_mapper->directory = $registry->get('fileroot', $app) . '/app/controllers';

        // Load application routes.
        $mapper = $this->_mapper;
        include $routeFile;
        if (file_exists($fileroot . '/config/routes.local.php')) {
            include $fileroot . '/config/routes.local.php';
        }

        // Match
        // @TODO Cache routes
        $path = $request->getPath();
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }
        $match = $this->_mapper->match($path);
        if (isset($match['controller'])) {
            $config->setControllerName(Horde_String::ucfirst($app) . '_' . Horde_String::ucfirst($match['controller']) . '_Controller');
            $config->setSettingsExporterName($settingsFinder->getSettingsExporterName($config->getControllerName()));
        } else {
            $config->setControllerName('Horde_Core_Controller_NotFound');
        }

        return $config;
    }
}
