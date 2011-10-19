<?php
class StudentController extends Corez_Base_BaseController
{
    protected $_roll_no;
    protected $_member_id;
    /**
     * @return the $_roll_no
     */
    public function getRoll_no ()
    {
        return $this->_roll_no;
    }
    /**
     * @return the $_member_id
     */
    public function getMember_id ()
    {
        return $this->_member_id;
    }
    /**
     * @param field_type $_roll_no
     */
    public function setRoll_no ($_roll_no)
    {
        $this->_roll_no = $_roll_no;
    }
    /**
     * @param field_type $_member_id
     */
    public function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /*
     * @about Interface.
     */
    public function indexAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $this->view->assign('controller', $this->_request->getControllerName());
        $this->view->assign('module', $this->_request->getModuleName());
    }
    public function getinfoAction ()
    {
        $request = $this->getRequest();
        $format = $request->getParam('format', 'json');
        $rollno = $request->getParam('rollno');
        if (isset($rollno)) {
            $result = Core_Model_DbTable_Student::getStudentInfo($rollno);
            switch (strtolower($format)) {
                case 'json':
                    $this->_helper->json($result);
                    return;
                case 'select' :
					/*echo '<select>';
					echo '<option>Select one</option>';
					foreach ( $result as $key => $row ) {
						echo '<option value="' . $row ['batch_start'] . '">' . $row ['batch_start'] . '</option>';
					}
					echo '</select>';
					*/
					return;
                case 'xml':
                    return;
                default:
                    $this->getResponse()
                        ->setException('Unsupported format request')
                        ->setHttpResponseCode(400);
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
        }
    }
    /*
     * RETURNS THE PROFILE OF GIVEN ROLL NUMBER
     */
    public function getprofileAction ()
    {
        $model = new Core_Model_Member_Student();
        $authInfo = Zend_Auth::getInstance()->getStorage()->read();
        $member_id = $authInfo['member_id'];
        /* 
       if (isset($member_id) 
        {
           
            $model->setMember_id($member_id);
            $model->getStudentInfo();
            
        }
        elseif ($this->getRequest()->getParam('roll_no'))
        {
            $model->setStudent_roll_no($this->_roll_no);
            $model->getMember_id();
            $model->getStudentInfo();
        }
        else 
        {
           throw new Exception('..................ABE ROLL NUMBER DAAL...........',Zend_Log::ERR);
        }
        */
        $this->setMember_id($member_id);
        $this->setRoll_no('2308009');
        $model->setMember_id($this->getMember_id());
        $model->setStudent_roll_no($this->getRoll_no());
        $model->getStudentInfo();
        $info = array('roll_no' => $model->getStudent_roll_no(), 
        'regn_no' => $model->getReg_no(), 
        'first_name' => $model->getFirst_name(), 
        'middle_name' => $model->getMiddle_name(), 
        'last_name' => $model->getLast_name(), 'gender' => $model->getGender(), 
        'phone_no' => $model->getContact_no(), 'dob' => $model->getDob(), 
        'email' => $model->getE_mail());
        $callback = $this->getRequest()->getParam('callback');
        echo $callback . '(' . $this->_helper->json($info, false) . ')';
         //$this->_helper->json($info);
    }
    public function searchAction ()
    {
        /*$student = new Core_Model_Member_Student();
        $dob_range = array('from' => 1, 'to' => 2);
        $rel_range = array('from' => 1, 'to' => 3);
        $nat_range = array('from' => 1, 'to' => 3);
        $r = array('check5'=>5,'cast_id' => $dob_range, 'check3' => 3, 
        'religion_id' => $rel_range,'nationality_id'=>$nat_range);
        $test = array('check4'=>5,'first_name' => 'AMRIT','nationality_id'=>1,'check1' => 2, 'check2' => 3);
        echo "<pre>";
        print_r($student->search($test, $r));
        echo "</pre>";*/
        $address = new Core_Model_Address();
        $address->setMember_id(1);
        $address->setAdress_type('COMMUNICATION');
        $address->initAddressInfo();
        $test = array('check4'=>5,'adress_type' => 'COMMUNICATION','nationality_id'=>1,'check1' => 2, 'check2' => 3);
        echo $address->getCity();
        echo "<pre>";
        print_r($address->search($test,null));
        echo "</pre>";
    }
}
   

