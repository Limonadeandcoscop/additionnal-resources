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
    public function init()
    {
        $this->_helper->db->setDefaultModelName('AdditionalResources');
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
	    	if (!strlen(trim($_POST['description']))) {
				$this->_helper->_flashMessenger(__('The "Description" field is required.'), 'error');
				return;	
	    	}

	    	$resource = new AdditionalResource;
	    	$resource->user_id = current_user()->id;
	    	$resource->item_id = $this->getParam('item_id');
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
		    	$name = $resource->getNextFileName($file);
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
	    }
   }    


}
