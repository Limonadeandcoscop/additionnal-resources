<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Values extends Zend_View_Helper_Abstract
{

	// Current item object
	private $_item;

	// Fields labels
	private $_fields;

	// Disable links in results ?
	private $_disable_links;

	public function __construct() {

		$this->_fields = parse_ini_file(ADDITIONAL_RESOURCES_PLUGIN_DIRECTORY.'/fields.ini');
	}

	/**
	 * Manage values calls
	 *
	 * @param $item The item object
	 * @return Array An array of values
	 *
	 */
    public function values($item, $key, $disable_links = false) {

    	// Check $item object
    	if (!($item instanceof Omeka_Record_AbstractRecord)) {
            throw new InvalidArgumentException('Invalid item passed to Values Helper.');
        }

        // Instanciante variable instance
    	$this->_item = $item;

    	$this->_disable_links = $disable_links;

    	$label = $this->_getLabel($key);

    	if (strpos($label, '[ITM]')) {
    		$set = "Item Type Metadata";
    		$label = trim(str_replace(" [ITM]", '', $label));
    	} else {
    		$set = "Dublin Core";
    	}

    	// Return values as array
    	if (method_exists($this, 'get_'.$key)) {

			// There exist a callback function, raw metadatas needs specific process
	    	$values = call_user_func(array($this, 'get_'.$key), $key);
	    	if (!is_array($values)) $values = array($values);

	    } else {

			// Returns raw metadatas
			$dcKey = str_replace('_', ' ', $key);
	    	$values = metadata($this->_item, array($set, ucwords($dcKey)), array('all' => true));
	    }

		return array(__($label) => $values);
    }


	/**
	 * Get the field label
	 *
	 * @param $key The key of the field
	 * @return String The label
	 *
	 */
	private function _getLabel($key) {

		return $this->_fields[$key];
	}


	/**
	 * Callback function for 'identifier' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_identifier($key)
	{
		$identifiers = metadata($this->_item, array('Dublin Core', 'Identifier'), array('all' => true));
		$identifiers = @preg_grep("/^(?!http.*$).*/", array_map("trim", $identifiers));
		$identifiers = @preg_grep("/^(?!ark:.*$).*/", array_map("trim", $identifiers));
        $identifiers = @preg_grep("/^(?!Alternative :.*$).*/", array_map("trim", $identifiers));
        $identifiers = @preg_grep("/^(?!<a.*$).*/", array_map("trim", $identifiers));
        $identifiers = array_values($identifiers);
        return $identifiers;
	}


	/**
	 * Callback function for 'type' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_type($key)
	{
		$types = metadata($this->_item, array('Dublin Core', 'Type'), array('all' => true));
		$types = @preg_grep("/^(?!image.*$).*/", array_map("trim", $types));
		$types = @preg_grep("/^(?!text.*$).*/", array_map("trim", $types));
		$types = array_values($types);

		if(@!$types[0]) return array();

        return $types;
	}


	/**
	 * Callback function for 'format' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_format($key)
	{
		$formats = metadata($this->_item, array('Dublin Core', 'Format'), array('all' => true));
		$formats = array_filter($formats, function($string) {return strpos($string, '/') === false;});
		return $formats;
	}


	/**
	 * Callback function for 'alternative_identifier' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_alternative_identifier($key)
	{
		$identifiers = metadata($this->_item, array('Dublin Core', 'Identifier'), array('all' => true));
		return preg_filter('/^Alternative : (.*)/', '$1', $identifiers);
	}



	/**
	 * Callback function for 'source' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_source($key)
	{
		$identifiers = metadata($this->_item, array('Dublin Core', 'Identifier'), array('all' => true));
		return @preg_grep("/^http:\/\//", $identifiers)[0];
	}


	/**
	 * Callback function for 'date_start' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_date_start($key)
	{
		$temporalCoverage = metadata($this->_item, array('Dublin Core', 'Temporal Coverage'), array('all' => true));
		$datesStart = array();
		foreach ($temporalCoverage as $date) {
			$d = explode(' - ', $date);
			@$datesStart[] = $d[0];
		}
		return $datesStart;
	}


	/**
	 * Callback function for 'date_end' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_date_end($key)
	{
		$temporalCoverage = metadata($this->_item, array('Dublin Core', 'Temporal Coverage'), array('all' => true));

		$datesEnd = array();

		foreach ($temporalCoverage as $date) {

			$d = explode(' - ', $date);

			if (is_string($d)) continue;

			if(is_array($d) && count($d)>1) {
				@$datesEnd[] = $d[1];
			} else {
				@$datesEnd[] = $d[0];
			}
		}
		return $datesEnd;
	}


	/**
	 * Callback function for 'scope_and_content' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_scope_and_content($key)
	{
		$descriptions = metadata($this->_item, array('Dublin Core', 'Description'), array('all' => true));

		$scopeAndContent = array();
		foreach ($descriptions as $description) {
			if (!preg_match_all('/^Arrangement/', $description)) {
				$scopeAndContent[] = $description;
			}
		}
		return $scopeAndContent;
	}


	/**
	 * Callback function for 'arrangement' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_arrangement($key)
	{
		$descriptions = metadata($this->_item, array('Dublin Core', 'Description'), array('all' => true));

		$arrangement = array();
		foreach ($descriptions as $description) {
			if (preg_match_all('/^Arrangement : /', $description)) {
				$arrangement[] = ucfirst(str_replace('Arrangement : ', '', $description));
			}
		}
		return $arrangement;
	}


	/**
	 * Callback function for 'relations_originals' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_relations_originals($key)
	{
		$relations = metadata($this->_item, array('Dublin Core', 'Relation'), array('all' => true));

		$originals = array();
		foreach ($relations as $relation) {
			if (preg_match_all('/^Originals : /', $relation)) {
				$originals[] = str_replace('Originals : ', '', $relation);
			}
		}
		return $originals;
	}


	/**
	 * Callback function for 'relations_copies' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_relations_copies($key)
	{
		$relations = metadata($this->_item, array('Dublin Core', 'Relation'), array('all' => true));

		$copies = array();
		foreach ($relations as $relation) {
			if (preg_match_all('/^Copies : /', $relation)) {
				$copies[] = str_replace('Copies : ', '', $relation);
			}
		}
		return $copies;
	}



	/**
	 * Callback function for 'subject_access_points' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_subject_access_points($key)
	{
		$subjects	= metadata($this->_item, array('Dublin Core', 'Subject'), array('all' => true));

		$res = array();
		foreach ($subjects as $subject) {
			if (preg_match_all('/^Subject : /', $subject)) {
				$res[] = trim(str_replace('Subject : ', '', $subject));
			}
		}
		return $res;
	}


	/**
	 * Callback function for 'name_access_points' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_name_access_points($key)
	{
		$subjects	= metadata($this->_item, array('Dublin Core', 'Subject'), array('all' => true));

		$names = array();
		foreach ($subjects as $subject) {
			if (preg_match_all('/^Name : /', $subject)) {
				$names[] = trim(str_replace('Name : ', '', $subject));
			}
		}
		return $names;
	}


	/**
	 * Callback function for 'access_rights' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	
	private function get_access_rights($key)
	{
		if (plugin_is_active('OaipmhHarvester')) {
			$item = OaipmhHarvesterPlugin::getTopParentItem($this->_item);
			$repository = metadata($item, array('Dublin Core', 'Publisher'));
		}

		$prefix = _('Subject to the authorization of ');

		if ($this->_disable_links) return $prefix . $repository;

		if (strlen(trim($repository))) {
			$links = metadata($item, array('Dublin Core', 'Relation'), array('all' => true));
			foreach ($links as $link) {
				if(substr($link, 0, 7) == "http://") 
					break;
			}
		}

		if (isset($link)) {
			return $prefix . '<a target="_blank" class="repository-link" href="'.$link.'">'.$repository.'</a>';
		}
		
		return $prefix . $repository;
	}
	
	/**
	 * Callback function for 'creator' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_creator($key)
	{
		if (plugin_is_active('OaipmhHarvester')) {
			$item = OaipmhHarvesterPlugin::getTopParentItem($this->_item);
		} else {
			$item = $this->_item;
		}

		$creators = metadata($item, array('Dublin Core', 'Creator'));
		$link = metadata($item, array('Item Type Metadata', 'Creator Link'));

		$res = array();

		if ($this->_disable_links) return $creators;

		if (isset($link)) {
			return '<a target="_blank" class="repository-link" href="'.$link.'">'.$creators.'</a>';
		}
		
		return $res;
	}


	/**
	 * Callback function for 'related_descriptions' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_related_descriptions($key)
	{
		$links = metadata($this->_item, array('Dublin Core', 'Is Part Of'), array('all' => true));

		$res = array();
		foreach($links as $link) {
			$link = explode('[[', $link);
			if(count($link)>1) {
				$name = $link[0];
				$url  = trim(str_replace(']]', '', $link[1]));
				if ($this->_disable_links) {
					$res[] = $name;
				} else {
					$res[] = '<a target="_blank" class="related-description-link" href="'.$url.'">'.trim($name).'</a>';
				}
			} else {
				$res[] = $link[0];
			}
		}
		return $res;
	}	


	/**
	 * Callback function for 'publisher' key
	 *
	 * @param $key The key of the field
	 * @return Array An array of values
	 */
	private function get_publisher($key)
	{

		if (plugin_is_active('OaipmhHarvester')) {
			$item = OaipmhHarvesterPlugin::getTopParentItem($this->_item);
		} else {
			$item = $this->_item;
		}

		$publisher = metadata($item, array('Dublin Core', 'Publisher'));
		$link = metadata($item, array('Dublin Core', 'Relation'));

		$res = array();

		if ($this->_disable_links) return $publisher;

		if (isset($link)) {
			return '<a target="_blank" class="repository-link" href="'.$link.'">'.$publisher.'</a>';
		}
		
		return $res;
	}	
}


