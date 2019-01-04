<?php
/**
 * Introspection API controller for the Apps case
 *
 * Purposefully circumvents registry's own permission checking
 * Any needed authorization or authentication needs to be handled
 * by a PreFilter defined in routes.php / routes.local.php
 */
class Api_IntrospectionApps_Controller extends Api_Controller_Api
{
    public function processRequest(Horde_Controller_Request $request, Horde_Controller_Response $response)
    {
        $registry = $this->getInjector()->getInstance('Horde_Registry');
        $response->setBody(json_encode($registry->listApps(null, true, null)));
    }
}
