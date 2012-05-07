<?php
class Tnp_Model_MemberInfo_EmpTestRecord extends Tnp_Model_Generic
{
    protected $_test_record_id;
    protected $_member_id;
    protected $_employability_test_id;
    protected $_test_regn_no;
    protected $_test_total_score;
    protected $_test_percentile;
    protected $_mapper;
    /**
     * @return the $_test_record_id
     */
    public function getTest_record_id ()
    {
        return $this->_test_record_id;
    }
    /**
     * @param bool $throw_exception optional
     * @return the $_member_id
     */
    public function getMember_id ($throw_exception = null)
    {
        $member_id = $this->_member_id;
        if (empty($member_id) and $throw_exception == true) {
            $message = 'Member_id is not set';
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $member_id;
        }
    }
    /**
     * @return the $_employability_test_id
     */
    public function getEmployability_test_id ()
    {
        return $this->_employability_test_id;
    }
    /**
     * @return the $_test_regn_no
     */
    public function getTest_regn_no ()
    {
        return $this->_test_regn_no;
    }
    /**
     * @return the $_test_total_score
     */
    public function getTest_total_score ()
    {
        return $this->_test_total_score;
    }
    /**
     * @return the $_test_percentile
     */
    public function getTest_percentile ()
    {
        return $this->_test_percentile;
    }
    /**
     * @param field_type $_test_record_id
     */
    public function setTest_record_id ($_test_record_id)
    {
        $this->_test_record_id = $_test_record_id;
    }
    /**
     * @param field_type $_member_id
     */
    public function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @param field_type $_employability_test_id
     */
    public function setEmployability_test_id ($_employability_test_id)
    {
        $this->_employability_test_id = $_employability_test_id;
    }
    /**
     * @param field_type $_test_regn_no
     */
    public function setTest_regn_no ($_test_regn_no)
    {
        $this->_test_regn_no = $_test_regn_no;
    }
    /**
     * @param field_type $_test_total_score
     */
    public function setTest_total_score ($_test_total_score)
    {
        $this->_test_total_score = $_test_total_score;
    }
    /**
     * @param field_type $_test_percentile
     */
    public function setTest_percentile ($_test_percentile)
    {
        $this->_test_percentile = $_test_percentile;
    }
    /**
     * Sets Mapper
     * @param Tnp_Model_Mapper_EmpTestInfo_Record $mapper
     * @return Tnp_Model_MemberInfo_EmpTestRecord
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * gets the mapper from the object class
     * @return Tnp_Model_Mapper_EmpTestInfo_Record
     */
    public function getMapper ()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Tnp_Model_Mapper_EmpTestInfo_Record());
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
}