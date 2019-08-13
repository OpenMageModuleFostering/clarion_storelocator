<?php
/**
 * Storelocation model
 * 
 * @category    Clarion
 * @package     Clarion_Storelocator
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Storelocator_Model_Storelocator extends Mage_Core_Model_Abstract
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('clarion_storelocator/storelocator');
    }
    
    /**
     * Check if store exists based on its name
     *
     * @param $storeName store name
     * @param $storeId store id
     * @return boolean
     */
    public function storeExists($storeName, $storeId = null)
    {
        $result = $this->_getResource()->storeExists($storeName, $storeId);
        return (is_array($result) && count($result) > 0) ? true : false;
    }
    
}