<?php
/**
 * @category   Auth
 * @package    Plugin_Acl
 * @subpackage Loader
 * @copyright  Copyright (c) 2009-2010 HeAvi
 */
/**
 * Check the access privilage of user.
 * 
 * It by default assumes a user as guest (if not logged in). It stores all roles and resources
 * in application cache. Creates a new user-specific role with parent roles from main-ACL and 
 * stores the new ACL in user\'s session storage along with identity.
 * 
 * Finally, it pick user ACL from user\'s session and check access rights. If allowed, it return
 * otherwise redirects to ErrorController -> noaccessAction with exceptions.
 */
class Auth_Plugin_Acl_Loader extends Zend_Controller_Plugin_Abstract
{
    /**
     * 
     * Guest account.
     * Always keep it in lower case.
     * @var const
     */
    const GUEST = 'guest';
    const AUTH_URL = '/authenticate';
    /**
     * preDispatch() - Check the access privilage of user.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return boolean 
     * @throws Zend_Exception on access denied.
     */
    public function preDispatch ()
    {
        $auth = Zend_Auth::getInstance();
        $authId = $auth->getStorage()->read();
        $request = self::getRequest();
        $actionName = strtolower($request->getActionName());
        $actionName = strtolower($request->getActionName());
        $controllerName = strtolower($request->getControllerName());
        if (substr($actionName, 0, 4) == 'fill' or
         substr($actionName, 0, 3) == 'get' or
         'authenticate' == strtolower($controllerName) or
         'error' == strtolower($controllerName)) {
            return;
        }
    
        if (! Zend_Session::isDestroyed()) {
            self::initUserAcl();
            self::check();
        } else {
            throw new Zend_Exception('Session is destroyed.', Zend_Log::WARN);
        }
    }
    /**
     * getCache() - Fetch cache from registry.
     *
     * @param  string $cacheName
     * @return Zend_Cache
     */
    public static function getCache ($cacheName = 'database')
    {
        return Zend_Registry::get('cacheManager')->getCache($cacheName);
    }
    /**
     * getDb() - Fetch cache from registry.
     *
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public static function getDb ()
    {
        return Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource(
        'db');
    }

    /**
     * getLogger() - Fetch logger from registry.
     *
     * @return Zend_Log
     */
    public static function getLogger () {
        return Zend_Registry::get('logger');
    }
    
    protected function initUserAcl ()
    {
        $authContent = Zend_Auth::getInstance()->getStorage()->read();
        if (! is_array($authContent)) {
            self::getLogger()->debug('Fresh visitor');
			$guestID = Authz_Resource_Acl_Guest::GUEST_ID;
            $authAcl = new Zend_Session_Namespace('authAcl');
            if (! isset($authAcl->authId)) {
                $authAcl->redirectedFrom = array_intersect_key($this->getRequest()->getParams(),
                                                        $this->getRequest()->getUserParams());
                                                        
                 $authAcl->redirectedParams = array_diff_key($this->getRequest()->getParams(),
                                                        $this->getRequest()->getUserParams());
                self::getLogger()->debug('Redirecting to "'.self::AUTH_URL.'", redirecting from');
                self::getLogger()->debug($authAcl->redirectedFrom);
                $this->getResponse()->setRedirect(self::AUTH_URL.'/welcomeguest', 303);
                return;
            } else {
                Zend_Registry::get('logger')->debug('User has logged in auth module');
            }
            self::updateACL($authAcl->authId);
        }
    }
    protected function updateACL($authId) {
    	$commonAcl = self::getCache()->load('commonAcl');
        if ($commonAcl === false) {
            $commonAcl = self::initAcl();
        } else {
            Zend_Registry::get('logger')->debug('Common ACL from Cache.');
        }
        $userInfo = self::getUserInfo($authId);
        /*
         * Create user specific acl and save in its session.
         */
        $commonAcl->addRole($authId, $userInfo['roles']);
        $userInfo['acl'] = $commonAcl;
        
        $lastLogin = new Zend_Session_Namespace('last');
        $userInfo['last'] = $lastLogin->lastLogin;
        Zend_Auth::getInstance()->getStorage()->write($userInfo);
        
        //Setting userID in cookie so that apache can get it in log files.
        preg_match('/[^.]+\.[^.]+$/', $_SERVER['SERVER_NAME'], $domain);
        setcookie('identity', $authId, null, null, ".$domain[0]", null, true);
        Zend_Registry::get('logger')->debug('Specific ACL saved in session.');;
    }
    /**
     * initAcl() - Initialize common ACL and save to cache.
     *
     * @return Zend_Acl
     */
    protected static function initAcl ()
    {
        $cache = self::getCache();
        $db = self::getDb();
        $selectRes = new Zend_Db_Select($db);
        $roleResources = $selectRes->from('role_resource')
            ->query(Zend_Db::FETCH_GROUP)
            ->fetchAll();
        //Zend_Registry::get ( 'logger' )->notice ( 'Role and Resources:' );
        //Zend_Registry::get ( 'logger' )->debug ( $roleResources );
        $acl = new Zend_Acl();
        //Seperatly added Guest to make it parent of all.
        $acl->addRole(self::GUEST);
        foreach ($roleResources as $roleKey => $resources) {
            $role = strtolower($roleKey);
            if (! (self::GUEST === $role)) {
                $acl->addRole($role, self::GUEST);
            }
            foreach ($resources as $key => $resource) {
                $res = strtolower(
                $resource['module_id'] . '_' . $resource['controller_id'] . '_' .
                 $resource['action_id']);
                if (! $acl->has($res)) {
                    $acl->addResource($res);
                }
                $acl->allow($role, $res);
            }
        }
        $cache->save($acl, 'commonAcl');
        Zend_Registry::get('logger')->debug('Common ACL cached.');
        return $acl;
    }
    /**
     * getUserRoles() - Fetch User roles from database.
     *
     * @param string $user_id
     * @return array $userRole Array of Roles, department_id,userType
     */
    public static function getUserInfo ($user_id)
    {
        $userInfo['identity'] = $user_id;
        $userInfo['roles'][] = self::GUEST;
        $db = self::getDb();
        $objSelect = new Zend_Db_Select($db);
        $dbInfo = $objSelect->from('user_role', 'role_id')
            ->join('auth_user', '`user_role`.`user_id` = `auth_user`.`user_id`', 
        array('department_id', 'user_type_id'))
            ->where("`user_role`.`user_id` = ?", $user_id)
            ->query()
            ->fetchAll();
        Zend_Registry::get('logger')->debug('Common ACL from Cache.');
        if (count($dbInfo)) {
            $userInfo['department_id'] = $dbInfo[0]['department_id'];
            $userInfo['userType'] = $dbInfo[0]['user_type_id'];
            foreach ($dbInfo as $row => $data) {
                $userInfo['roles'][] = strtolower($data['role_id']);
            }
        } else {
            Zend_Registry::get('logger')->warn('No role found for ' . $user_id);
        }
        return $userInfo;
    }
    public function check ()
    {
        $request = $this->_request;
        $authContent = Zend_Auth::getInstance()->getStorage()->read();
        //if ($_COOKIE['last'] == $authContent['last']) {
        if (isset($authContent['acl'])) {
            $userAcl = $authContent['acl'];
            if ($userAcl instanceof Zend_Acl) {
                $reqResource = strtolower(
                $request->getModuleName() . '_' . $request->getControllerName() .
                 '_' . $request->getActionName());
                if ($userAcl->has($reqResource)) {
                    if ($userAcl->isAllowed($authContent['identity'], 
                    $reqResource)) {
                        return true;
                    } else {
                        if ('development' != strtolower(APPLICATION_ENV)) {
                            throw new Exception(
                            'ACL denied "' . str_ireplace('_', '/', 
                            $reqResource) . '" to ' . $authContent['identity'] .
                             ' at ' . $_SERVER['REMOTE_ADDR'], Zend_Log::ALERT);
                        }
                        Zend_Registry::get('logger')->notice(
                        'ACL ERROR: BLOCKED BY ACL. BUT FUNCTIONAL DUE TO DEVELOPMENT ENV.');
                    }
                } else {
                    if ('development' != strtolower(APPLICATION_ENV)) {
                        throw new Exception(
                        'RESOURCE "' . str_ireplace('_', '/', $reqResource) .
                         '" is not found in ACL', Zend_Log::WARN);
                    }
                    Zend_Registry::get('logger')->notice(
                    'ACL ERROR: RESOURCE "' .
                     str_ireplace('_', '/', $reqResource) .
                     '" is not found in ACL');
                }
            } else {
                throw new Exception(
                'Not a valid instance of ACL. var_export(userACL)=>' .
                 var_export($userAcl, true), Zend_Log::ERR);
            }
        } else {
            throw new Exception('User Acl not found.', Zend_Log::ERR);
        }
        /*} else {
            $this->getResponse()->setRedirect('/authenticate/logout', 303);
        }*/
    }
}