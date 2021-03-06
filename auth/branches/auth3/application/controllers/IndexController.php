<?php
class IndexController extends Authz_Base_BaseController
{
    /**
     * 
     * @var int
     */
    protected $_member_id;
    protected $_user_name;
    protected $_user_type;
    protected $_department_id;
    /**
     * @return the $_member_id
     */
    protected function getMember_id ()
    {
        return $this->_member_id;
    }
    /**
     * @return the $_user_name
     */
    protected function getUser_name ()
    {
        return $this->_user_name;
    }
    /**
     * @return the $_user_type
     */
    protected function getUser_type ()
    {
        return $this->_user_type;
    }
    /**
     * @return the $_department_id
     */
    protected function getDepartment_id ()
    {
        return $this->_department_id;
    }
    /**
     * @param int $_member_id
     */
    protected function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @param field_type $_user_name
     */
    protected function setUser_name ($_user_name)
    {
        $this->_user_name = $_user_name;
    }
    /**
     * @param field_type $_user_type
     */
    protected function setUser_type ($_user_type)
    {
        $this->_user_type = $_user_type;
    }
    /**
     * @param field_type $_department_id
     */
    protected function setDepartment_id ($_department_id)
    {
        $this->_department_id = $_department_id;
    }
    public function init ()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $authInfo = Zend_Auth::getInstance()->getStorage()->read();
            if (! empty($authInfo['department_id'])) {
                $this->setDepartment_id($authInfo['department_id']);
            }
            if (! empty($authInfo['identity'])) {
                $this->setUser_name($authInfo['identity']);
            }
            if (! empty($authInfo['userType'])) {
                $this->setUser_type($authInfo['userType']);
            }
            if (! empty($authInfo['member_id'])) {
                $this->setMember_id($authInfo['member_id']);
            }
        }
    }
    public function indexAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = $this->getMember_id();
        if (! empty($member_id)) {
            if (! empty($params['registeredMember']) and
             ($params['registeredMember'] == 'true')) {} else {
                $this->_redirect('http://core.aceambala.com/member/register');
            }
        }
    }
    public function markauthAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $this->_redirect('/index/markcore');
    }
    public function markcoreAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_redirect('http://core.aceambala.com/index/bounce');
    }
    public function finddepartmentAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $format = $params['format'];
        $member_id = $params['member_id'];
        $department_id = $this->findDepartmentId($member_id);
        switch ($format) {
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' .
                 $this->_helper->json($department_id, false) . ')';
                break;
            case 'json':
                $this->_helper->json($department_id);
                break;
            case 'log':
                Zend_Registry::get('logger')->debug('No Format provided');
                break;
            default:
                ;
                break;
        }
    }
    public function markacadAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_redirect('http://academic.aceambala.com/index/bounce');
    }
    public function marktnpAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_redirect('http://tnp.aceambala.com/index/bounce');
    }
    private function findDepartmentId ($member_id)
    {
        $auth_user = new Auth_Model_Member_User();
        $auth_user->setMember_id($member_id);
        return $auth_user->fetchDepartment();
    }
}