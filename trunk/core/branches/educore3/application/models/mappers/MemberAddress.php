<?php
class Core_Model_Mapper_MemberAddress
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Core_Model_Mapper_MemberAddress
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
            $this->setDbTable('Core_Model_DbTable_MemberAddress');
        }
        return $this->_dbTable;
    }
    /**
     * Fetches Address details of a Member
     * 
     * @param integer $member_id
     */
    public function fetchInfo ($member_id, $address_type)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = new Core_Model_DbTable_Class();
        $address_table = $db_table->info('name');
        $required_cols = array('member_id', 'postal_code', 'city', 'district', 
        'state', 'address', 'adress_type');
        $select = $adapter->select()
            ->from($address_table, $required_cols)
            ->where('member_id = ?', $member_id)
            ->where('address_type=?', $address_type);
        $address_info = array();
        $address_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        return $address_info[$member_id];
    }
    public function save ($prepared_data)
    {
        $dbtable = $this->getDbTable();
        try {
            $row_id = $dbtable->insert($prepared_data);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
?>