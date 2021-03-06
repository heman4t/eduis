<?php
class Acad_Model_Mapper_Member_Student
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Member_Student
     */
    public function setDbTable ($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (! $dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    /**
     * Get registered Zend_Db_Table instance
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Acad_Model_DbTable_Members');
        }
        return $this->_dbTable;
    }
    public function memberIdCheck ($member_id)
    {
        $member_ids = $this->getDbTable()->find($member_id);
        if (0 == count($member_ids)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Fetches CRITICAL information of a Student
     * 
     * @param integer $member_id
     */
    public function fetchCriticalInfo ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $req_cols = array('member_id', 'member_type_id', 'first_name', 
        'last_name', 'middle_name', 'dob', 'blood_group', 'gender', 
        'religion_id', 'cast_id', 'nationality_id', 'join_date', 'relieve_date', 
        'image_no', 'is_active');
        $table_name = $this->getDbTable()->info('name');
        $select = $adapter->select()
            ->from($table_name, $req_cols)
            ->where('member_id = ?', $member_id);
        $student_info = array();
        $student_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        if (empty($student_info)) {
            return false;
        } else {
            return $student_info[$member_id];
        }
    }
    public function fetchStudents ($exact_property = null, $property_range = null)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $stu_table = $db_table->info('name');
        $required_cols = array('member_id');
        $select = $adapter->select()->from($stu_table, $required_cols);
        if (! empty($property_range)) {
            foreach ($property_range as $key => $range) {
                if (! empty($range['from'])) {
                    $select->where("$key >= ?", $range['from']);
                }
                if (! empty($range['to'])) {
                    $select->where("$key <= ?", $range['to']);
                }
            }
        }
        if (! empty($exact_property)) {
            foreach ($exact_property as $exact_key => $exact_range) {
                $select->where("$exact_key = ?", $exact_range);
            }
        }
        $member_ids = array();
        $member_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $member_ids;
    }
    public function fetchQualifications ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = new Acad_Model_DbTable_MemberQualification();
        $qualification_table = $db_table->info('name');
        $required_cols = array('qualification_id');
        $select = $adapter->select()
            ->from($qualification_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $qualifications = array();
        $qualifications = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $qualifications;
    }
    public function fetchSubjects ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = new Acad_Model_DbTable_StudentSubject();
        $stu_subj_table = $db_table->info('name');
        $required_cols = array('subject_id');
        $select = $adapter->select()
            ->from($stu_subj_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $qualifications = array();
        $qualifications = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $qualifications;
    }
    public function fetchCompetitiveExams ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = new Acad_Model_DbTable_StudentCompetitiveExam();
        $stu_comp_table = $db_table->info('name');
        $required_cols = array('exam_id');
        $select = $adapter->select()
            ->from($stu_comp_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $qualifications = array();
        $qualifications = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $qualifications;
    }
    public function save ($prepared_data)
    {
        $dbtable = $this->getDbTable();
        return $dbtable->insert($prepared_data);
    }
    public function update ($prepared_data, $member_id)
    {
        $dbtable = $this->getDbTable();
        $where = 'member_id = ' . $member_id;
        return $dbtable->update($prepared_data, $where);
    }
}