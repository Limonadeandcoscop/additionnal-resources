<?php
/**
 * AdditionalResourceFile
 *
 * @copyright Copyright 2017-2020 Limonade & Co <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package AdditionalResourceFile
 */

/**
 * A TranslateItems row.
 *
 * @package Omeka\Plugins\AdditionalResourceFile
 */
class AdditionalResourceFile extends Omeka_Record_AbstractRecord
{
    public $resource_id;
    public $size;
    public $original_filename;
    public $name;
    public $created;
}
