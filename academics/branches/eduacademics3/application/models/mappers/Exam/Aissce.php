<?php
class Acad_Model_Mapper_Exam_Aissce
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Course_Dmc
     */
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Exam_Aissce
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
            $this->setDbTable('Acad_Model_DbTable_Aissce');
        }
        return $this->_dbTable;
    }
    /**
     * fetches AISSCE Exam info
     * @param Acad_Model_Exam_Aissce $aissce
     *@todo make memberId as basis
     */
    public function fetchMemberExamInfo (Acad_Model_Exam_Aissce $aissce)
    {
        $member_id = $aissce->getMember_id();
        $adapter = $this->getDbTable()->getAdapter();
        $required_fields = array('member_id', 'board', 'board_roll_no', 
        'marks_obtained', 'total_marks', 'percentage', 'pcm_percent', 
        'passing_year', 'school_rank', 'remarks', 'institution', 
        'migration_date', 'city_name', 'state_name');
        $table = $this->getDbTable()->info('name');
        $select = $adapter->select()
            ->from($table, $required_fields)
            ->where('member_id = ?', $member_id);
        $member_exam_info = array();
        $member_exam_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        if (sizeof($member_exam_info) == 0) {
            throw new Exception(
            'NO TWELFTH DATA EXISTS FOR MEMBER_ID' . $member_id . '.');
        } else {
            return $member_exam_info[$member_id];
        }
    }
    /**
     * 
     * Enter description here ...
     * @param array $options
     * @param Acad_Model_Exam_Aissce $aissce
     */
    public function save ($options, Acad_Model_Exam_Aissce $aissce = null)
    {
        $dbtable = $this->getDbTable();
        $cols = $dbtable->info('cols');
        //$db_options is $options with keys renamed a/q to db_columns
        $db_options = array();
        foreach ($options as $key => $value) {
            $db_options[$this->correctDbKeys($key)] = $value;
        }
        $db_options_keys = array_keys($db_options);
        $recieved_keys = array_intersect($db_options_keys, $cols);
        $data = array();
        foreach ($recieved_keys as $key_name) {
            $str = "get" . ucfirst($this->correctModelKeys($key_name));
            $data[$key_name] = $aissce->$str();
        }
        //$adapter = $this->getDbTable()->getAdapter();
        //$where = $adapter->quoteInto("$this->correctDbKeys('member_id') = ?", $student->getMember_id());
        $adapter = $dbtable->getAdapter();
        $table = $dbtable->info('name');
        $adapter->beginTransaction();
        try {
            $sql = $adapter->insert($table, $data);
            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }
    }
    /**
     * Enter description here ...
     * @param Acad_Model_Exam_Aissce $aissce
     * @param array $property_range Example :array('name'=>array('from'=>n ,'to'=>m));
     * here 'from' stands for >= AND 'to' stands for <=
     * 
     */
    public function fetchStudents (Acad_Model_Exam_Aissce $aissce, 
    array $setter_options = null, array $property_range = null)
    {
        $correct_db_options = array();
        foreach ($setter_options as $k => $val) {
            $correct_db_options[$this->correctDbKeys($k)] = $val;
        }
        $correct_db_options_keys = array_keys($correct_db_options);
        $correct_db_options1 = array();
        foreach ($property_range as $k1 => $val1) {
            $correct_db_options1[$this->correctDbKeys($k1)] = $val1;
        }
        $correct_db_options1_keys = array_keys($correct_db_options1);
        $merge = array_merge($correct_db_options_keys, 
        $correct_db_options1_keys);
        $table = $this->getDbTable()->info('name');
        //1)get column names of twelfth present in arguments received
        $twelfth_col = $this->getDbTable()->info('cols');
        $twelfth_intrsctn = array();
        $twelfth_intrsctn = array_intersect($twelfth_col, $merge);
        $adapter = $this->getDbTable()->getAdapter();
        $select = $adapter->select()->from($table, 'member_id');
        if (count($correct_db_options1)) {
            foreach ($correct_db_options1 as $key => $range) {
                if (! empty($range['from'])) {
                    $select->where("$key >= ?", $range['from']);
                }
                if (! empty($range['to'])) {
                    $select->where("$key <= ?", $range['to']);
                }
            }
        }
        if (count($correct_db_options)) {
            foreach ($correct_db_options as $property_name => $value) {
                $getter_string = 'get' .
                 ucfirst($this->correctModelKeys($property_name));
                $aissce->$getter_string();
                $condition = $property_name . ' = ?';
                $select->where($condition, $value);
            }
        }
        $result = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (! count($result)) {
            $search_error = 'No results match your search criteria.';
            throw new Exception($search_error, Zend_Log::WARN);
        } else {
            return $result;
        }
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
}