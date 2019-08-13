<?php
/**
 * Storelocation Resource Model
 * 
 * @category    Clarion
 * @package     Clarion_Storelocator
 * @author      Clarion Magento Team <pandurang.babar@clariontechnologies.co.in>
 * 
 */
class Clarion_Storelocator_Model_Resource_Storelocator extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('clarion_storelocator/storelocator', 'storelocator_id');
    }
    
    /**
     * Process storelocator data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Clarion_Storelocator_Model_Resource_Storelocator
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'storelocator_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('clarion_storelocator/storelocator_store'), $condition);

        return parent::_beforeDelete($object);
    }
    
    /**
     * Check if store exists
     *
     * @param $storeName store name
     * @param $storelocatorId storelocaror id
     * @return array|false
     */
    public function storeExists($storeName, $storelocatorId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();

        if(empty($storelocatorId)){
            $binds = array(
              'name' => $storeName,
            );
            
             $select->from($this->getMainTable())
             ->where('(name = :name)');
        } else {
            $binds = array(
              'name' => $storeName,
              'storelocator_id'  => (int) $storelocatorId,
            ); 
            
            $select->from($this->getMainTable())
            ->where('(name = :name)')
            ->where('storelocator_id <> :storelocator_id');
        }
        return $adapter->fetchRow($select, $binds);
    }
    
    /**
     * Assign storelocator to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Clarion_Storelocator_Model_Resource_Storelocator
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('clarion_storelocator/storelocator_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'storelocator_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'storelocator_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }
    
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $storelocatorId
     * @return array
     */
    public function lookupStoreIds($storelocatorId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('clarion_storelocator/storelocator_store'), 'store_id')
            ->where('storelocator_id = ?',(int)$storelocatorId);
        return $adapter->fetchCol($select);
    }
    
    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }
        return parent::_afterLoad($object);
    }
    
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Page $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('clarion_storelocator_store' => $this->getTable('clarion_storelocator/storelocator_store')),
                $this->getMainTable() . '.storelocator_id = clarion_storelocator_store.storelocator_id',
                array())
                ->where('status = ?', 1)
                ->where('clarion_storelocator_store.store_id IN (?)', $storeIds)
                ->order('clarion_storelocator_store.store_id DESC')
                ->limit(1);
        }
        return $select;
    }
    
}