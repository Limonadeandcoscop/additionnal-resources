<?php
/**
 * AdditionalResources
 *
 * @copyright Copyright 2017-2020 Limonade & Co <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package AdditionalResources
 */

/**
 * A TranslateItems row.
 *
 * @package Omeka\Plugins\AdditionalResource
 */
class AdditionalResource extends Omeka_Record_AbstractRecord
{
    public $item_id;
    public $user_id;
    public $title;
    public $description;
    public $created;


    /**
     * Get the next filename for this resource
     *
     * @param Array File the from file object
     * @return Integer The next name
     */
    public function getNextFileName($file) {

        $files = get_db()->getTable('AdditionalResourceFile')->findBy(array('resource_id' => $this->id));

        if (!$files) {
            $id = $this->id . '_1';
        } else {
            $lastFile = $files[count($files)-1];
            $name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $lastFile->name);
            $lastId = trim(explode('_', $name)[1]);
            $id = $this->id .'_' . ($lastId + 1);
        }

        $path_parts = pathinfo($file['name']);
        $extension = $path_parts['extension'];

        return $id.'.'.$extension; 
    }    



    /**
     * Get the resources of an item
     *
     * @param Item $item The item object
     * @return Array of AdditionalResource entries
     */
    public static function getItemResources($item) {

        $resources = get_db()->getTable('AdditionalResource')->findBy(array('item_id' => $item->id));
        return $resources;
    }    


    /**
     * Check if a top level item has PDF file attached
     *
     * @param Item the Item object
     * @return Boolean
     */
    public static function itemHasPdfFile($item) {

        $name = 'pdf_'.$item->id.'.pdf'; 
        $path = ADDITIONAL_RESOURCES_UPLOADS_PATH.'/'.$name;
        return file_exists($path);
    }    


    /**
     * Get the files of this ressource
     */
    public function getFiles() {

        $files = get_db()->getTable('AdditionalResourceFile')->findBy(array('resource_id' => $this->id));
        return $files;
    }    


    /**
     * Delete files attached to resource before delete the resource
     */
    public function beforeDelete()
    {
        $files = $this->getFiles();
        
        if (count($files)) {
            foreach ($files as $file) {
                $file->delete();
            }
        }
    }

}
