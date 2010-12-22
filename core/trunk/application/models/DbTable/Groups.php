<?php
/**
 * @category   EduIS
 * @package    Core
 * @subpackage Groups
 * @since	   0.1
 */
class Core_Model_DbTable_Groups extends Zend_Db_Table {
	protected $_name = 'groups';
	const TABLE_NAME = 'groups';
	
	public static function getClassGroups($department, $degree, $fetchColumn = NULL) {
        $sql = self::getDefaultAdapter()->select()
                                          ->from(self::TABLE_NAME, 'group_id')
                                          ->where('`department_id` = ?',$department)
                                          ->where('`degree_id` = ?',$degree);
        if ($fetchColumn) {
        	return $sql->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        } else {
        	return $sql->query()->fetchAll();
        }
    }
}