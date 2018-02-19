<?php
/**
 * TranslateItems
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The translate_items table.
 *
 * @package Omeka\Plugins\TranslateItems
 */
class Table_TranslateItem extends Omeka_Db_Table
{
    /**
     * Check if an item is a translation
     * @params Item|int $item The item object or item ID
     * @return true | false
     */
    public function isTranslation($item)
    {
        $item_id = ($item instanceof Item) ? $item->id : $item;

        $row = $this->findBy(array('item_id' => $item_id));

        if ($row[0]->original == 0)
            return true;

        return false;
    }


    /**
     * Check if an item is an original
     * @params Item|int $item The item object or item ID
     * @return true | false
     */
    public function isOriginal($item)
    {
        return !$this->isTranslation($item);
    }


    /**
     * Retuns the language of an item
     * @param Item|int $item The item object or item ID
     * @return String The language or FALSE
     */
    public function getLanguage($item)
    {
        $item_id = ($item instanceof Item) ? $item->id : $item;

        $row = $this->findBy(array('item_id' => $item_id));

        if (isset($row[0]))
            return $row[0]->language;

        return false;
    }


    /**
     * Retuns the translations of an item
     * @param Item|int $item The item object or item ID
     */
    public function getTranslations($item)
    {
        $item_id = ($item instanceof Item) ? $item->id : $item;

        $rows = $this->findBy(array('original_item_id' => $item_id, 'original' => 0));

        return $rows;
    }

    /**
     * Retuns the original item of a translation
     * @param Item|int $item The item object or item ID
     * @return Item The Item object
     */
    public function getOriginalItem($item)
    {

        if ($this->isOriginal($item)) return $item;

        $item_id = ($item instanceof Item) ? $item->id : $item;

        $row = $this->findBy(array('item_id' => $item_id, 'original' => 0));

        $original_item_id = $row[0]->original_item_id;

        $item = get_record_by_id("Item", $original_item_id);

        return $item;
    }


    /**
     * Check if an item as a translation in a given language (whether it's an original or not)
     * If "$item" is an original, return the row where original_item_id is "item ID", otherwise return the row where original_item_id is "original item ID"
     * @param Item|int $item The item object or item ID
     * @param String $code The code of the language, for example "en"
     * @return Interger The ID of the item
     */
    public function getTranslationItemId($item, $code)
    {
        $item_id = ($item instanceof Item) ? $item->id : $item;

        if ($this->isOriginal($item)) {
            $row = $this->findBy(array('original_item_id' => $item_id, 'language' => $code));
        } else {
            $originalItemId = $this->getOriginalItem($item);
            $row = $this->findBy(array('original_item_id' => $originalItemId->id, 'language' => $code));
        }

        if(isset($row) && count($row))
            return $row[0]['item_id'];

        return false;

    }

    /**
     * Returns the link to create a translation
     * If "$item" is an original, create the link with "item ID", otherwise create the link with "original item ID"
     * @param Item|int $item The item object or item ID
     * @param String $code The code of the language, for example "en"
     * @return String An URL
     */
    public function getUrlToCreateTranslation($item, $code)
    {
        if ($this->getTranslationItemId($item, $code)) {
            throw new Exception('The item '.$item->id. ' as already a translation in '.getLanguageName($code).' ('.$this->getTranslationItemId($item, $code).')');
        }

        if ($this->isOriginal($item)) {
            $url = url('/items/add/o/'.$item->id.'/l/'.$code);
        } else {
            $originalItemId = $this->getOriginalItem($item);
            $url = url('/items/add/o/'.$originalItemId->id.'/l/'.$code);
        }

        return $url;
    }


    /**
     * Returns the edit an existant translation
     * If "$item" is an original, create the link with "item ID", otherwise create the link with "original item ID"
     * @param Item|int $item The item object or item ID
     * @param String $code The code of the language, for example "en"
     * @return String An URL
     */
    public function getUrlToEditTranslation($item, $code)
    {
        if (!$this->getTranslationItemId($item, $code)) {
            throw new Exception('The item '.$item->id. ' can\'t be edited because it has no translation in '.getLanguageName($code));
        }

        if ($this->isOriginal($item)) {
            $url = url('/items/edit/' . $this->getTranslationItemId($item, $code));
        } else {
            $url = url('/items/edit/' . $this->getTranslationItemId($item, $code));
        }

        return $url;
    }


    /**
     * Delete entries in the translate_items table
     * If the $item is an original, delete the item and all its translations
     * @param Item $item The item object
     */
    public function deleteItem($item)
    {
        if (!$item && get_class($item) != 'Item') {
            throw new Exception('Unable to delete item translation : item param could not be empty');
        }

        // Retrieve original item
        if ($this->isOriginal($item)) {

            $translations = $this->getTranslations($item);

            // Delete translation items
            foreach($translations as $translation) {
                $translationItem = get_record_by_id("Item", $translation->item_id);
                if(get_class($translationItem) == 'Item') {
                    $translationItem->delete();
                }
            }

            $this->query("DELETE FROM `{$this->getTableName()}` WHERE original_item_id = " . $item->id);

        } else {

            $this->query("DELETE FROM `{$this->getTableName()}` WHERE item_id = " . $item->id);
        }
    }





}
