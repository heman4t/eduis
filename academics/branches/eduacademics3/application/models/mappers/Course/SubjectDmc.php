<?php
class Acad_Model_Mapper_Course_SubjectDmc
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Course_SubjectDmc
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
            $this->setDbTable('Acad_Model_DbTable_SubjectDmc');
        }
        return $this->_dbTable;
    }
    /**
     * 
     * @todo
     */
    public function save ()
    {}
    /**
     * 
     * @param Acad_Model_Course_SubjectDmc $subjectDmc
     */
    public function fetchSubjectMarksHistory (
    Acad_Model_Course_SubjectDmc $subjectDmc)
    {
        $member_id = $subjectDmc->getMember_id();
        $subjCode = $subjectDmc->getSubject_code();
        $appearType = $subjectDmc->getAppear_type();
        if (! isset($member_id) or ! isset($subjCode)) {
            throw new Exception(
            'Insufficient data provided.. both memberId and subCode are required');
        } else {
            $requiredFields = array('dmc_id', 'marks', 'appear_type');
            $adapter = $this->getDbTable()->getDefaultAdapter();
            $select = $adapter->select()
                ->from('dmc_record', $requiredFields)
                ->where('member_id = ?', $member_id)
                ->where('subject_code = ?', $subjCode);
            if (isset($appearType)) {
                $select->where('appear_type = ?', $appearType);
            }
            $subjectMarksHistory = $select->query()->fetchAll(
            Zend_Db::FETCH_UNIQUE);
            //Zend_Registry::get('logger')->debug($subjectMarksHistory);
            return $subjectMarksHistory;
        }
    }
    /**
     * @todo  incomplete
     * 
     * @param Acad_Model_Course_SubjectDmc $subjectDmc
     */
    public function fetchInfo (Acad_Model_Course_SubjectDmc $subjectDmc)
    {
        /**
         * call getmarks history and add following to query 
         * where marks = (marks[0] or marks[1]
         * and get all dmc ids corresponding to a appear type
         * @var unknown_type
         */
        $member_id = $subjectDmc->getMember_id();
        $subjCode = $subjectDmc->getSubject_code();
        $marks = $subjectDmc->getMarks();
        if (! isset($member_id) or ! isset($subjCode) or ! isset($marks)) {
            throw new Exception(
            'Insufficient data provided..  memberId, subCode and subjMarks are ALL required');
        } else {
            $dmc_info_fields = array('custody_date', 'custody_date', 
            'is_granted', 'grant_date', 'recieving_date', 'is_copied', 
            'dispatch_date');
            $dmc_record_fields = array('member_id', 'subject_code', 'marks', 
            'appear_type');
            $adapter = $this->getDbTable()->getAdapter();
            $select = $adapter->select()
                ->from($this->getDbTable()
                ->info('name'), $dmc_info_fields)
                ->joinInner('dmc_record', 'dmc_info.dmc_id = dmc_record.dmc_id', 
            $dmc_record_fields)->where('member_id = ?',$member_id)->where('member_id = ?',$member_id);
            $dmc_info = array();
            $dmc_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
            return $dmc_info[$member_id];
        }
    }
    /**
     * 
     * @param Acad_Model_Course_SubjectDmc $subjectDmc
     */
    public function fetchPassedSemestersInfo (
    Acad_Model_Course_SubjectDmc $subjectDmc)
    {
        $member_id = $subjectDmc->getMember_id();
        $requiredFields = array('semester_id', 'dmc_id', 'marks_obtained', 
        'scaled_marks', 'total_marks');
        $adapter = $this->getDbTable()->getAdapter();
        $select = $adapter->select()
            ->from('dmc_total_marks', $requiredFields)
            ->joinInner('dmc_record', 
        'dmc_total_marks.dmc_id = dmc_record.dmc_id', 'member_id')
            ->where('member_id = ?', $member_id);
        $considered_dmc_records = array();
        $considered_dmc_records = $select->query()->fetchAll(
        Zend_Db::FETCH_UNIQUE);
        //Zend_Registry::get('logger')->debug($considered_dmc_records);
        if (sizeof($considered_dmc_records) == 0) {
            throw new Exception(
            'No passed semesters record exist for ' . $subjectDmc->getMember_id());
        }
        return $considered_dmc_records;
    }
    /**
     * @todo incomplete
     * @param Acad_Model_Course_SubjectDmc $subjectDmc
     */
    public function fetchMemberId (Acad_Model_Course_SubjectDmc $subjectDmc)
    {}
    /**
     * @todo incomplete
     * @param Acad_Model_Course_SubjectDmc $subjectDmc
     */
    public function fetchMemberDmcRecords (
    Acad_Model_Course_SubjectDmc $subjectDmc)
    {
        $dmcRecordFields = array('dmc_id', 'appear_type', 
        'marks as marks_scored_uexam');
        $internalMarksFields = array('subject_code', 
        'marks_scored as marks_scored_internal', 
        'marks_suggested as marks_suggested_internal');
        $cond = 'internal_marks.member_id = dmc_record.member_id AND internal_marks.subject_code = dmc_record.subject_code';
        $adapter = $this->getDbTable()->getAdapter();
        $member_id = $subjectDmc->getMember_id();
        $department_id = $subjectDmc->getDepartment_id();
        $programme_id = $subjectDmc->getProgramme_id();
        $semester_id = $subjectDmc->getSemster_id();
        $appear_type = $subjectDmc->getAppear_type();
        $select = $adapter->select()
            ->from('dmc_record', $dmcRecordFields)
            ->joinInner('internal_marks', $cond, $internalMarksFields)
            ->where('internal_marks.member_id = ?', $member_id)
            ->where('department_id = ?', $department_id)
            ->where('programme_id = ?', $programme_id)
            ->where('semester_id = ?', $semester_id)
            ->where('appear_type = ?', $appear_type);
        $memberDmcRecords = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        Zend_Registry::get('logger')->debug($memberDmcRecords);
        return $memberDmcRecords;
    }
}
