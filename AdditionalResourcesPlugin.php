<?php
/**
 * AdditionalResources
 *
 * Provides the ability to translate items in backoffice
 *
 * @copyright Copyright 2017-2020 Limonade & Co <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package AdditionalResources
 */

// define('TRANSLATE_ITEMS_DIR', dirname(__FILE__));
// define('JAVASCRIPT_ADMIN_DIR', WEB_PLUGIN.'/TranslateItems/views/admin/javascripts/');

// require_once TRANSLATE_ITEMS_DIR . '/helpers/TranslateItems.php';

/**
 * The AdditionalResources plugin.
 * @package Omeka\Plugins\AdditionalResources
 */

/**
 * The AdditionalResources plugin.
 * @package Omeka\Plugins\AdditionalResources
 */
class AdditionalResourcesPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'define_acl',
        'admin_items_show_sidebar',
    );


    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
    );


    /**
     * Add the Additional Resources link to the admin main navigation.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Additional Resources'),
            'uri' => url('additional-resources'),
            'resource' => 'AdditionalResources_Index',
            'privilege' => 'browse'
        );
        return $nav;
    }


    /**
     * Define the ACL.
     * 
     * @param Omeka_Acl
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        
        $additionalResourcesResource = new Zend_Acl_Resource('AdditionalResources_Index');
        $acl->add($additionalResourcesResource);
        
        $acl->allow(array('super', 'admin'), array('AdditionalResources_Index'));
        $acl->allow(null, 'AdditionalResources_Index', 'show');
    }    


    /**
    * Appended to admin item show pages.
    *
    * @param array $args
    */
    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];
        echo get_view()->partial('index/_sidebar.php', array('item' => $item));
    }
    
    


}