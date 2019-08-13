<?php
/**
 * Storelocation Resource Model
 * 
 * @category    Clarion
 * @package     Clarion_Storelocator
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Storelocator_Model_Resource_Storelocator extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('clarion_storelocator/storelocator', 'store_id');
    }
    
    /**
     * Check if store exists
     *
     * @param $storeName store name
     * @param $storeId store id
     * @return array|false
     */
    public function storeExists($storeName, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();

        if(empty($storeId)){
            $binds = array(
              'name' => $storeName,
            );
            
             $select->from($this->getMainTable())
             ->where('(name = :name)');
        } else {
            $binds = array(
              'name' => $storeName,
              'store_id'  => (int) $storeId,
            ); 
            
            $select->from($this->getMainTable())
            ->where('(name = :name)')
            ->where('store_id <> :store_id');
        }
        return $adapter->fetchRow($select, $binds);
    }
}