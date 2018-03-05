<?php
/**
 * AdditionalResources
 *
  * @copyright Copyright 2017-2020 Limonade & Co <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package AdditionalResources
 */

/**
 * The AdditionalResources index controller class.
 *
 * @package AdditionalResources
 */
class AdditionalResources_IndexController extends Omeka_Controller_AbstractActionController
{    
	private $_allowedExtensions = array('pdf', 'jpg', 'jpeg', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv');

    public function init()
    {
        $this->_helper->db->setDefaultModelName('AdditionalResource');
    }
    
    public function indexAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        return;
    }


    public function addAction()
    {
    	if (!$this->getParam('item_id')) throw new Exception('Item ID id required');

    	if ($this->_request->isPost()) {
	    	if (!strlen(trim($_POST['title']))) {
				$this->_helper->_flashMessenger(__('The "title" field is required.'), 'error');
				return;	
	    	}

	    	$resource = new AdditionalResource;
	    	$resource->user_id = current_user()->id;
	    	$resource->item_id = $this->getParam('item_id');
	    	$resource->title = $_POST['title'];
	    	$resource->description = $_POST['description'];
	    	$resource->save();	    	

	    	// Re-arrange multiple files uploads
			foreach ($_FILES['files'] as $key => $all) {
		        foreach( $all as $i => $val ){
		            $files[$i][$key] = $val;    
		        }    
		    }

		    // Add resource files
		    foreach ($files as $file) {

		    	if (!$file['name']) continue;

		    	$name = $resource->getNextFileName($file);
        		$extension = strtolower(trim(pathinfo($file['name'])['extension']));

        		if (!in_array($extension, $this->_allowedExtensions)) continue;

		    	$cmd = "mv ".$file['tmp_name']." ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
		    	shell_exec($cmd);
		    	$cmd = "chmod 755 ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
		    	shell_exec($cmd);
		    	$resourceFile = new AdditionalResourceFile;
		    	$resourceFile->resource_id = $resource->id;		    	
		    	$resourceFile->size = $file['size'];
		    	$resourceFile->original_filename = $file['name'];
		    	$resourceFile->name = $name;
		    	$resourceFile->save();	    	
		    }

		    // Redirect to item page
		   	$this->_helper->redirector->gotoUrl('/items/show/'.$this->getParam('item_id'));

	    }
   	}    

	public function editAction()
   	{
   		if (!$this->getParam('resource_id')) throw new Exception('Resource ID id required');

   		$resource = get_record_by_id("AdditionalResource", $this->getParam('resource_id'));
   		$this->view->resource = $resource;

   		if ($this->_request->isPost()) {

	    	if (!strlen(trim($_POST['title']))) {
				$this->_helper->_flashMessenger(__('The "Title" field is required.'), 'error');
				return;	
	    	} else {
	    		$resource->title = $_POST['title'];
	    		$resource->description = $_POST['description'];
	    		$resource->save();
	    	}

	    	// Delete selected files
	    	if (isset($_POST['delete-pdf'])) {
		    	foreach ($_POST['delete-pdf'] as $fileId => $value) {
		    		if ($value == '1') {
		    			$file = get_record_by_id("AdditionalResourceFile", $fileId);
		    			$file->delete();
		    		}
		    	}
		    }

	    	// Re-arrange multiple files uploads
			foreach ($_FILES['files'] as $key => $all) {
		        foreach( $all as $i => $val ){
		            $files[$i][$key] = $val;    
		        }    
		    }

		    // Add resource files
		    foreach ($files as $file) {

		    	if (!strlen(trim($file['name']))) continue;
		    	$name = $resource->getNextFileName($file);
        		$extension = strtolower(trim(pathinfo($file['name'])['extension']));

        		if (!in_array($extension, $this->_allowedExtensions)) continue;		    	
		    	$cmd = "mv ".$file['tmp_name']." ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
		    	shell_exec($cmd);
		    	$cmd = "chmod 755 ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
		    	shell_exec($cmd);
		    	$resourceFile = new AdditionalResourceFile;
		    	$resourceFile->resource_id = $resource->id;		    	
		    	$resourceFile->size = $file['size'];
		    	$resourceFile->original_filename = $file['name'];
		    	$resourceFile->name = $name;
		    	$resourceFile->save();	    	
		    }		    

		    // Redirect to item page
		   	$this->_helper->redirector->gotoUrl('/items/show/'.$resource->item_id);
	    }

   	}

    /**
     * Ask for user confirmation before deleting a record.
     * 
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @uses self::_getDeleteConfirmMessage()
     */
    public function deleteConfirmAction()
    {
    	$this->_helper->viewRenderer->setNoRender();

        $isPartial = $this->getRequest()->isXmlHttpRequest();
        $record = $this->_helper->db->findById();
        $form = $this->_getDeleteForm();
        $confirmMessage = __('Are you sure you want to delete this item ?');
      
        $this->view->assign(compact('confirmMessage','record', 'isPartial', 'form'));
        $this->render('common/delete-confirm', null, true);
    }

    /**
     * Redirect to another page after a record is successfully deleted.
     *
     * The default is to redirect to this controller's browse page.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    protected function _redirectAfterDelete($record)
    {
        $this->_helper->redirector->gotoUrl('/items/show/'.$record->item_id);
    }    


	public function addPdfAction()
    {
    	if ($this->_request->isPost()) {
			$file = $_FILES['pdf-file'];
    		if (!strlen(trim($file['name']))) {
    			$this->_helper->_flashMessenger(__('The PDF file is required.'), 'error');
				return;		
    		} else {
    			$extension = strtolower(trim(pathinfo($file['name'])['extension']));
    			if ($extension != 'pdf') {
    				$this->_helper->_flashMessenger(__('Only PDF file are allowed.'), 'error');
					return;		
    			} else {
    				if ($item_id = $this->getParam('item_id')) {
    					$name = 'pdf_'.$item_id.'.pdf';
    					$cmd = "mv ".$file['tmp_name']." ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
				    	shell_exec($cmd);
				    	$cmd = "chmod 755 ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
				    	shell_exec($cmd);
				    	$this->_helper->redirector->gotoUrl('/items/show/'.$item_id);
    				}
    			}
    		}
    	} else {
    		if ($item_id = $this->getParam('item_id')) {
    			$name = 'pdf_'.$item_id.'.pdf';	
    			$path = ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
    			echo file_exists($path);
    		}
    	}
    }
    

    public function editPdfAction()
    {
    	$item = get_record_by_id("Item", $this->getParam('item_id'));
    	$this->view->item = $item;

    	if ($this->_request->isPost()) {
			$file = $_FILES['pdf-file'];
    		if (!strlen(trim($file['name']))) {
    			$this->_helper->_flashMessenger(__('The PDF file is required.'), 'error');
				return;		
    		} else {
    			$extension = strtolower(trim(pathinfo($file['name'])['extension']));
    			if ($extension != 'pdf') {
    				$this->_helper->_flashMessenger(__('Only PDF file are allowed.'), 'error');
					return;		
    			} else {
    				if ($item_id = $this->getParam('item_id')) {
    					$name = 'pdf_'.$item_id.'.pdf';
    					unlink(ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name);
    					$cmd = "mv ".$file['tmp_name']." ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
				    	shell_exec($cmd);
				    	$cmd = "chmod 755 ".ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
				    	shell_exec($cmd);
				    	$this->_helper->redirector->gotoUrl('/items/show/'.$item_id);
    				}
    			}
    		}
    	} else {
    		if ($item_id = $this->getParam('item_id')) {
    			$name = 'pdf_'.$item_id.'.pdf';	
    			$path = ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
    			echo file_exists($path);
    		}
    	}
    }


    /**
     * Suggest titles for search inputs
     *
     * @param Integer (Ajax) $text The text input by user
     * @return JSON
     */
    public function autocompleteAction()
    {
        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // if (!$this->_request->isXmlHttpRequest()) return;

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        // Get DB object
        $db = $this->_helper->db;

        // Retrive param
        if (!($text = $this->getParam('text'))) return;

        $tableElementTexts = $db->getTable('ElementText');

        $sql = "SELECT record_id, text from ".$tableElementTexts->getTableName()." WHERE text LIKE '".$text."%' AND element_id = 50 AND record_type ='Item' AND record_id IN (SELECT id FROM ".$db->getTable('OaipmhHarvester_Hierarchy')->getTableName().")";

        $res = $tableElementTexts->fetchAll($sql);

        echo json_encode($res);
    }    
}
