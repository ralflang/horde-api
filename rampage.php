<?php
/**
 * Rampage routing script v2
 * This is based off the original horde core controller framework
 * Lv0 provide very basic json api framework (rest0)
 * Lv1 provide auth and permissions via prefilters
 * Lv2 detect and handle old-style controllers correctly
 *
 * We use a custom RequestMapper
 */

require_once __DIR__ . '/lib/Application.php';
Horde_Registry::appInit('api', array('authentication' => 'none'));

$request = $injector->getInstance('Horde_Controller_Request');

$runner = $injector->getInstance('Horde_Controller_Runner');

// The original rampage framework loads RequestMapper::getRequestConfiguration via a binding on injector -> get('Horde_Controller_RequestConfiguration)
// We explicitly load our derived RequestMapper
// -- find app and controller by Api name first before falling back to app names in the slug
// After finding the controller, a SettingsFinder will look for the correct SettingsExporter class to load.
// The SettingsExporter can be specific to a controller or any of its parents

//$config = $injector->getInstance('Horde_Controller_RequestConfiguration');
$mapper = $injector->getInstance('Api_RequestMapper');
$config = $mapper->getRequestConfiguration($injector);

// The runner gets the injector, the request and a requestconfiguration (which class to use, which SettingsExporter to use)
// The SettingsExporter is actually loaded by injector, so the app pushed to top of stack can deliver any non-global dependencies
// The exporter in turn can add any required bindings back into the injector

// Finally the SettingsExporter exports actual Pre/Postfilter instances (not names) to the FilterRunner 
// which will run prefilters and, if applicable, the actual controller, finally postfilters before actually returning anything

$response = $runner->execute($injector, $request, $config);

$responseWriter = $injector->getInstance('Horde_Controller_ResponseWriter');
$responseWriter->writeResponse($response);
