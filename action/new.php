<?php
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Pierre Spring <pierre.spring@liip.ch>
 */

class action_plugin_npd_new extends DokuWiki_Action_Plugin {
    var $fck_location = false;
    var $helper       = false;
    var $toc          = false;
    var $cache        = true;

    function getInfo()
    {
        return confToHash(dirname(__FILE__).'../plugin.info.txt');
    }

    function register(&$controller)
    {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'fckw_index');
    }

    function fckw_index(&$event)
    {
        global $ACT;
        // we only change the edit behaviour
        if ($ACT != 'index' || !isset($_REQUEST['npd']) ){
            return;
        }
        global $conf;
        $conf['template_original'] = $conf['template'];
        $conf['template'] = "../plugins/npd/tpl";
    }
}
