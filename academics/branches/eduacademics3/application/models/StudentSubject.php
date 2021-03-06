<?php
class Acad_Model_StudentSubject extends Acad_Model_Generic
{
    protected $_student_subject_id;
    protected $_member_id;
    protected $_class_id;
    protected $_subject_id;
    protected $_mapper;
    /**
     * @return the $_student_subject_id
     */
    public function getStudent_subject_id ($throw_exception = null)
    {
        $student_subject_id = $this->_student_subject_id;
        if (empty($student_subject_id) and $throw_exception == true) {
            $message = '_student_subject_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $student_subject_id;
        }
    }
    /**
     * @param bool $throw_exception optional
     * @return the $_member_id
     */
    public function getMember_id ($throw_exception = null)
    {
        $member_id = $this->_member_id;
        if (empty($member_id) and $throw_exception == true) {
            $message = 'Member_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $member_id;
        }
    }
    /**
     * @return the $_class_id
     */
    public function getClass_id ($throw_exception = null)
    {
        $class_id = $this->_class_id;
        if (empty($class_id) and $throw_exception == true) {
            $message = '_class_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $class_id;
        }
    }
    /**
     * @return the $_subject_id
     */
    public function getSubject_id ($throw_exception = null)
    {
        $subject_id = $this->_subject_id;
        if (empty($subject_id) and $throw_exception == true) {
            $message = '_subject_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $subject_id;
        }
    }
    /**
     * @param field_type $_student_subject_id
     */
    public function setStudent_subject_id ($_student_subject_id)
    {
        $this->_student_subject_id = $_student_subject_id;
    }
    /**
     * @param field_type $_member_id
     */
    public function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @param field_type $_class_id
     */
    public function setClass_id ($_class_id)
    {
        $this->_class_id = $_class_id;
    }
    /**
     * @param field_type $_subject_id
     */
    public function setSubject_id ($_subject_id)
    {
        $this->_subject_id = $_subject_id;
    }
    /**
     * Sets Mapper
     * @param Acad_Model_Mapper_StudentSubject $mapper
     * @return Acad_Model_StudentSubject
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * gets the mapper from the object class
     * @return Acad_Model_Mapper_StudentSubject
     */
    public function getMapper ()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Acad_Model_Mapper_StudentSubject());
        }
        return $this->_mapper;
    }
    /**
     * Provides correct db column names corresponding to model properties
     * @todo add correct names where required
     * @param string $key
     */
    protected function correctDbKeys ($key)
    {
        switch ($key) {
            /*case 'nationalit':
                return 'nationality';
                break;*/
            default:
                return $key;
                break;
        }
    }
    /**
     * Provides correct model property names corresponding to db column names
     * @todo add correct names where required
     * @param string $key
     */
    protected function correctModelKeys ($key)
    {
        switch ($key) {
            /*case 'nationality':
                return 'nationalit';
                break;*/
            default:
                return $key;
                break;
        }
    }
    /**
     * 
     */
    public function initInfo ()
    {}
    public function fetchClassIds ()
    {
        $member_id = $this->getMember_id();
        $subject_id = $this->getSubject_id();
        if (empty($member_id) or empty($subject_id)) {
            $error = 'Insufficient params supplied to fetchClassIds() function.Please provide a Member Id and a Subject id';
            throw new Exception($error, Zend_Log::ERR);
        } else {
            $class_ids = $this->getMapper()->fetchClassIds($member_id, 
            $subject_id);
            if (empty($class_ids)) {
                return false;
            } else {
                return $class_ids;
            }
        }
    }
    /**
     * Fetches Marks scored by the student in the given Subject,
     * A student may have studied a Subject more than Once but in Different classes,
     * Ex - Detained Student,therefore subject_id and class_id are required and must be set in the object.Furthermore, a Subject may have multiple Marks corresponding to Different Result_Type_Ids,therefore result_type_id is also required and must be provided as parameter
     * @param integer $result_type_id
     * @param boolean $considered optional 
     * 
     */
    public function fetchDMC ($result_type_id, $considered = null)
    {
        $member_id = $this->getMember_id(true);
        $subject_id = $this->getSubject_id(true);
        $class_id = $this->getClass_id(true);
        $student_subject_id = $this->fetchStudentSubjectId();
        if (empty($student_subject_id)) {
            return false;
        } else {
            $dmc_marks_obj = new Acad_Model_Course_DmcMarks();
            $dmc_marks_obj->setStudent_subject_id($student_subject_id);
            $dmc_marks_obj->setResult_type_id($result_type_id);
            if (isset($considered)) {
                $dmc_marks_obj->setIs_considered($considered);
            }
            $marks = $dmc_marks_obj->fetchInfo();
            if ($marks instanceof Acad_Model_Course_DmcMarks) {
                return $marks;
            } else {
                return false;
            }
        }
    }
    public function fetchStudentSubjectId ()
    {
        $member_id = $this->getMember_id(true);
        $class_id = $this->getClass_id(true);
        $subject_id = $this->getSubject_id(true);
        $stu_subj_id = $this->getMapper()->fetchStudentSubjectId($member_id, 
        $class_id, $subject_id);
        if (empty($stu_subj_id)) {
            return false;
        } else {
            return $stu_subj_id;
        }
    }
    /**
     * 
     * @todo
     * 
     */
    public function fetchSubjectsPassed ($class_id)
    {}
    public function fetchSubjectPassedStatus ()
    {
        $stu_sub_id = $this->fetchStudentSubjectId();
    }
    /**
     * Fetches Student_subject_id and Subject_id of All subjects studied by a student in an Academic Class
     *
     */
    public function fetchSubjects ()
    {
        $member_id = $this->getMember_id(true);
        $class_id = $this->getClass_id(true);
        $student_subjects = $this->getMapper()->fetchSubjects($member_id, 
        $class_id);
        if (empty($student_subjects)) {
            return false;
        } else {
            return $student_subjects;
        }
    }
    /**
     * 
     * Enter description here ...
     * @param array $subject_ids
     */
    public function assignSubjects (array $subject_ids)
    {
        $member_id = $this->getMember_id(true);
        $class_id = $this->getClass_id(true);
        foreach ($subject_ids as $subject_id) {
            $data = array('member_id' => $member_id, 'class_id' => $class_id, 
            'subject_id' => $subject_id);
            $this->getMapper()->save($data);
        }
    }
}