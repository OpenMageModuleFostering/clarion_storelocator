<?php
/**
 * Manage Storelocator grid block
 * 
 * @category    Clarion
 * @package     Clarion_Storelocator
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Storelocator_Block_Adminhtml_Storelocator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        /** This set’s the ID of our grid i.e the html id attribute of the <div>.
         * If you’re using multiple grids in a page then id needs to be unique.
         */
        $this->setId('StorelocatorGrid');
        
        /**
         * This tells which sorting column to use in our grid. Which column 
         * should be used for default sorting
         */
        $this->setDefaultSort('store_id');
        
        /**
         * The default sorting order, ascending or descending
         */
        $this->setDefaultDir('DESC');
        
        /**
         * this basically sets your grid operations in session. Example, if we 
         * were on page2 of grid or we had searched something on grid when 
         * refreshing or coming back to the page, the grid operations will 
         * still be there. It won’t revert back to its default form. 
         */
       $this->setSaveParametersInSession(true);
       $this->setUseAjax(true);
    }
    
    /**
     * Prepare storelocator grid collection object
     *
     * @return Clarion_Storelocator_Block_Adminhtml_Storelocator_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('clarion_storelocator/storelocator')->getCollection();
        /* @var $collection Clarion_Storelocator_Model_Resource_Storelocator_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare default grid column
     *
     * @return Clarion_Storelocator_Block_Adminhtml_Storelocator_Grid
     */
    protected function _prepareColumns()
    {
       /**
        * ‘id’ an unique id for column
        * ‘header’ is the name of the column
        * ‘index’ is the field from our collection. This ‘id’ column needs to be 
        * present in our collection’s models.
        */
        
        $enableDisable = Mage::getModel('clarion_storelocator/enabledisable')->toArray();
        
        $this->addColumn('store_id', array(
            'header'=>Mage::helper('clarion_storelocator')->__('Store Id'),
            'sortable'=>true,
            'type' => 'number',
            'index'=>'store_id'
        ));
        
        $this->addColumn('name', array(
            'header'=>Mage::helper('clarion_storelocator')->__('Store Name'),
            'sortable'=>true,
            'index'=>'name'
        ));
		
        $this->addColumn('country', array(
            'header'=>Mage::helper('clarion_storelocator')->__('Country'),
            'sortable'=>true,
            'index'=>'country',
            'type' => 'country',
        ));
        
        $this->addColumn('state', array(
            'header'=>Mage::helper('clarion_storelocator')->__('State'),
            'sortable'=>true,
            'index'=>'state'
        ));
        
        $this->addColumn('city', array(
            'header'=>Mage::helper('clarion_storelocator')->__('City'),
            'sortable'=>true,
            'index'=>'city'
        ));
        
        $this->addColumn('zipcode', array(
            'header'=>Mage::helper('clarion_storelocator')->__('Zipcode'),
            'sortable'=>true,
            'index'=>'zipcode'
        ));
        
        $this->addColumn('status', array(
            'header'=>Mage::helper('clarion_storelocator')->__('Status'),
            'index'     => 'status',
            'width'=>'100px',
            'type'      => 'options',
            'options'    => $enableDisable,
        ));
        
        /**
         * Adding Different Options To Grid Rows
         */
       $this->addColumn('action',
        array(
            'header'    => Mage::helper('clarion_storelocator')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('clarion_storelocator')->__('Edit'),
                    'url'     => array('base'=> '*/*/edit'),
                    'field'   => 'store_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
       ));
        
      //Import Export functionality
      $this->addExportType('*/*/exportCsv', Mage::helper('clarion_storelocator')->__('CSV'));
      $this->addExportType('*/*/exportXml', Mage::helper('clarion_storelocator')->__('XML'));
      
        return parent::_prepareColumns();
    }
    
    /**
     * Row click url. 
     * when user click on any rows of the grid it goes to a specific URL.
     * URL is of the editAction of your controller and it passed the row’s id as a parameter. 
     * @param object $row Data row object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('store_id' => $row->getId()));
    }
    
    /**
     * Mass Actions. 
     * 
     * These used basically to do operations on multiple rows together.
     */
    protected function _prepareMassaction()
    {
        /**
         * id is the database column that serves as the unique identifier throughout 
         * your data structure, including: db table, single product magento model
         * , and the collection.
         */
        $this->setMassactionIdField('store_id');
        
        /**
         * By using this we can set name of checkbox, used for selection. Which 
         * is used to pass all the ids to the controller.
         */
        $this->getMassactionBlock()->setFormFieldName('storeIds');
        
        /**
         * url - sets url for the delete action
         * confirm - This shows the user a confirm dialog before submitting the URL
         */
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('clarion_storelocator')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('clarion_storelocator')->__('Are you sure?')
        ));
        
        /**
         * Get the enable disable drop down array
         */
        $enableDisable = Mage::getModel('clarion_storelocator/enabledisable')->toOptionArray();
        
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('clarion_storelocator')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('clarion_storelocator')->__('Status'),
                         'values' => $enableDisable
                    )
              )
        ));
        return $this;
    }
    
    /**
     * Used for Ajax Based Grid
     * 
     * URL which is called in the Ajax Request, to the get
     *  the content of the grid. _current Uses the current module, controller, 
     * action and parameters.
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}