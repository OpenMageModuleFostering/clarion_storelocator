<?php
/**
 * Storelocation Collection Resource Model
 * 
 * @category    Clarion
 * @package     Clarion_Storelocator
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Storelocator_Model_Resource_Storelocator_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define collection model
     *
     */
    protected function _construct()
    {
        $this->_init('clarion_storelocator/storelocator');
    }
    
    /**
     * Specify filter by "is_visible" field
     *
     * @return Clarion_Storelocator_Model_Resource_Storelocator_Collection
     */
    public function addStatusFilter()
    {
        return $this->addFieldToFilter('status', 1);
    }
    
    /**
     * Prepare for displaying in list
     *
     * @param integer $page
     * @return Clarion_Storelocator_Model_Resource_Storelocator_Collection
     */
    public function prepareForList($page)
    {
        //Set collection page size
        $this->setPageSize(Mage::helper('clarion_storelocator')->getStoresPerPage());
        //Set current page
        $this->setCurPage($page);
        //Set select order
        $this->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);
        return $this;
    }
}