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

    private $_solrFacets = array('publisher' => 'repository', 'language' => 'language', 'name_access_points' => 'name', 'spatial_coverage' => 'name');

    private $_disableLinks = array('related_descriptions');

    private $_itemFields = array('identifier', 
                                 'alternative_identifier',
                                 'title',
                                 'date',
                                 'date_start',
                                 'date_end',
                                 'type',
                                 'format',
                                 'publisher',
                                 'creator',
                                 'scope_and_content',
                                 'language',
                                 'source',
                                 'bibliographic_citation',
                                 'related_descriptions',
                                 'name_access_points',
                                 'spatial_coverage',
                                 );    

    private $_fondsFields = array('identifier', 
                                 'alternative_identifier',
                                 'title',
                                 'date',
                                 'date_start',
                                 'date_end',
                                 'type',
                                 'format',
                                 'publisher',
                                 'creator',
                                 'provenance',
                                 'accrual_method',
                                 'scope_and_content',
                                 'accrual_policy',
                                 'accrual_periodicity',
                                 'arrangement',
                                 'access_rights',
                                 'is_referenced_by',
                                 'relations_originals',
                                 'relations_copies',
                                 'bibliographic_citation',
                                 'related_descriptions',
                                 'conforms_to',
                                 'status',
                                 'date_submitted',
                                 'source',
                                 "archivist's_note",
                                 'subject_access_points',
                                 'spatial_coverage',
                                 'name_access_points',
                                 );        


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


    /**
     * Return formatted values of the application
     *
     * @param Integer (url|ajax) $item-id The ID of the original item (in english)
     * @param String (url|ajax) $language The language code requested
     * @param Boolean (url|ajax) $related Get the related item ?
     * @param Boolean (url|ajax) $with_headers Add the title, the reference and the title in the results
     * @see _getValues()
     * @return JSON|Array according to result param
     */
    public function valuesAction() 
    {
        // Get item-id
        if (!($id = $this->getParam('item-id'))) {
            throw new Exception("Item ID is required");
        }

        // Get Item object
        if (!($item = get_record_by_id("Item", $id))) {
            throw new Exception("Invalid item");
        }            

        $related = $this->getParam('related');

        if ($related) {
            $item = OaipmhHarvesterPlugin::getTopParentItem($item);
        }

        // Get and check language code
        $language = $this->getParam('language');
        if ($language == 'en') unset($language);
        if (isset($language)) {
            if (!isValidLanguageCode($language)) {
                throw new Exception("Invalid language code : " . $language);
            } else {
                $translations = TranslateItemsPlugin::getOtherLanguagesOfItem($item);
                foreach($translations as $translation) {
                    if ($translation->language == $language) {
                        $translatedItemId = $translation->item_id;
                    }
                }
                if (isset($translatedItemId)) {
                    if (!($item = get_record_by_id("Item", $translatedItemId))) {
                        throw new Exception("Invalid translated item");
                    }  
                }
                // Else, there's no translation in this language, returns the original item
            }
        }

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        $values['values'] = $this->_getValues($item);

        if ($this->getParam('with_headers')) {
            $values['headers']['identifier']    = @array_shift($this->view->values($item, 'identifier'));
            $values['headers']['type']          = @array_shift($this->view->values($item, 'publisher'));
            $values['headers']['title']         = @array_shift($this->view->values($item, 'title'));
        }

        $values['item_id'] = $item->id;

        if ($this->getParam('debug')) {
            echo '<pre>';
            print_r($values);
            echo '</pre>';
        } else {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            echo json_encode($values);
        }
    }


    /**
     * Return formatted values of the application
     *
     * @param Item $item The item object
     * @return Array A multidimensionnal array containing values
     */
    private function _getValues($item)
    {
        $ini = parse_ini_file(ADDITIONAL_RESOURCES_PLUGIN_DIRECTORY.'/fields.ini', true);

        $isTopLevel = OaipmhHarvesterPlugin::isTopLevelItem($item);

        $res = array();
        foreach ($ini as $section => $fields) {

            foreach ($fields as $key => $field) {

                if ($isTopLevel) {
                    if (!in_array($key, $this->_fondsFields)) {
                        continue;
                    }
                } else {
                    if (!in_array($key, $this->_itemFields)) {
                        continue;
                    }
                }
                
                if (array_key_exists($key, $this->_solrFacets) || in_array($key, $this->_disableLinks) ) {
                    $values = $this->view->values($item, $key, true);
                } else {
                    $values = $this->view->values($item, $key);
                }

                $temp = $values;
                $temp = array_shift($temp);

                foreach($values as $k => $value) {

                    $text = array();
                    foreach($value as $v) {
                        if (array_key_exists($key, $this->_solrFacets)) {
                            $facet = $this->_solrFacets[$key];
                            $text[$k][] = $this->_solrLink($v, $facet);
                        } else {
                            $text[$k][] = nl2br($v);
                        }
                    }
                    if (count($text)) {
                        $res[$section][] = $text;
                    }
                }
                /*
                if (count($temp)) {
                    $res[$section][] = $values;
                }
                */
            }
             
        }
        return $res;        
    }

    private function _solrLink($value, $solrFacet)
    {
        $url = url('solr-search?q=&facet='.$solrFacet.'%3A'.htmlspecialchars('"', ENT_QUOTES).$value.htmlspecialchars('"', ENT_QUOTES));
        return '<a style="color:orangered;" href="'.$url.'">'.$value.'</a>';
    }


}
