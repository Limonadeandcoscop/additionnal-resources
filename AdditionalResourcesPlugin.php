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

define('ADDITIONAL_RESOURCES_UPLOADS_PATH', FILES_DIR . '/uploads');
define('ADDITIONAL_RESOURCES_UPLOADS_URL', WEB_FILES . '/uploads');


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
        'public_items_show',
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

        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->AdditionalResourceFiles}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `resource_id` int(10) unsigned NOT NULL,
          `size` int(30) unsigned NOT NULL,
          `original_filename` mediumtext COLLATE utf8_unicode_ci,
          `name`  mediumtext COLLATE utf8_unicode_ci,
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
        $db->query("DROP TABLE IF EXISTS `$db->AdditionalResources`");
        $db->query("DROP TABLE IF EXISTS `$db->AdditionalResourceFiles`");
    }

    

    /**
    * Appended to admin item show pages.
    *
    * @param array $args
    */
    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];

        $resources = AdditionalResource::getItemResources($item);

        $html = get_view()->partial('index/_sidebar.php', array('item' => $item, 'resources' => $resources));

        $html = str_replace("\t", '', $html); // remove tabs
        $html = str_replace("\n", '', $html); // remove new lines
        $html = str_replace("\r", '', $html); // remove carriage returns
        $html = addslashes($html);

        echo '<script>';
        echo '  jQuery(document).ready(function($) {';
        echo '    $("<p>'.$html.'</p>").insertAfter($(".public-featured.panel"))';
        echo '  });';
        echo '</script>';
    }
    
    
    /**
     * Display additional resources on frontoffice
     */
    public function hookPublicItemsShow($args)
    {
        $item = $args['item'];
        $resources =  AdditionalResource::getItemResources($item);
        echo get_view()->partial('additional-resources/show.php', array('resources' => $resources));
    }


}