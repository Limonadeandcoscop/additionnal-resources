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
        //$this->_helper->db->setDefaultModelName('SimplePagesPage');
    }
    
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        return;
    }
}
