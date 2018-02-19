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
 * @package Omeka\Plugins\CollectionTree
 */
class AdditionalResource extends Omeka_Record_AbstractRecord
{
    public $item_id;
    public $user_id;
    public $description;
    public $created;
}
