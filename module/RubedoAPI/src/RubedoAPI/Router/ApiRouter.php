<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2014, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license.
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */

namespace RubedoAPI\Router;

use Rubedo\Services\Manager;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface;

class ApiRouter implements RouteInterface
{
    public function assemble(array $params = array(), array $options = array())
    {
        //todo: implement this
    }

    public function match(RequestInterface $request)
    {
        //ensure that no url like /dam or /image can match, without complex queries.
        if (!method_exists($request, 'getUri'))
            return null;
        if (!preg_match('#^/api/v\d+/.+#', $request->getUri()->getPath()))
            return null;
        $segmentList = explode('/',trim($request->getUri()->getPath(),'/'));
        $params = array();
        $params['controller'] = 'RubedoApi\\Frontoffice\\Controller\\Api';
        $params['action'] = 'index';
        $params['version'] = $segmentList[1];
        unset($segmentList[0], $segmentList[1]);
        $params['api'] = &$segmentList;
        try {
            $params['id'] = new \MongoID($segmentList[count($segmentList)+1]); //last element, index start at 2
            unset ($segmentList[count($segmentList)+1]);
        } catch(\Exception $e) {}
        $match = new RouteMatch($params);

        return $match;
    }

    public static function factory($options = array())
    {
        return new static();
    }

    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}