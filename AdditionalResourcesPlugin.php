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
        'install',
        'uninstall',
        'admin_items_show_sidebar',
    );


    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        
    );


    /**
     * Install the plugin (create tables on database)
     */
    public function hookInstall()
    {
        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->AdditionalResources}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `item_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          `description` mediumtext COLLATE utf8_unicode_ci,
          `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);
    }


    /**
     * Uninstall the plugin (drop tables from database)
     */
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->AdditionalResources` ";
        $db->query($sql);
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