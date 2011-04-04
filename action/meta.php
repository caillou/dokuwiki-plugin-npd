<?php
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Pierre Spring <pierre.spring@liip.ch>
 */

class action_plugin_npd_meta extends DokuWiki_Action_Plugin {
    var $js_location      = false;

    function getInfo()
    {
        return confToHash(dirname(__FILE__).'../plugin.info.txt');
    }

    function register(&$controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'meta');
    }

    /**
     * meta adds the js file needed to display the "Create New Page" 
     *
     * @param mixed $event
     * @access public
     * @return void
     */
    function meta(&$event)
    {
        $plugin = $this->getPluginName();
        $js_base_dir = DOKU_BASE.'lib/plugins/'.$plugin.'/js/';
        $this->js_location = $js_base_dir . 'button.js';
        $event->data['script'][] =
            array(
                'type'=>'text/javascript',
                'charset'=>'utf-8',
                '_data'=>'',
                'src'=> $this->js_location
            );
        return;
    }
}
