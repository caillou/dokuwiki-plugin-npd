<?php
/**
 * DokuWiki Default Template
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
if (plugin_isdisabled('npd') || !($npd =& plugin_load('helper', 'npd'))) die();

$conf['template'] = $conf['template_original'];
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang']?>">
<head>
    <meta charset=utf-8>
    <title>
        <?php tpl_pagetitle()?>
        [<?php echo strip_tags($conf['title'])?>]
    </title>
    <?php tpl_metaheaders()?>
    <script src="<?php echo DOKU_BASE ?>lib/plugins/npd/tpl/script.js"></script>

    <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>
    <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo DOKU_BASE ?>lib/plugins/npd/tpl/style.css"/>
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="<?php echo DOKU_BASE ?>lib/plugins/npd/tpl/style_ie7.css"/>
    <![endif]-->
</head>
<body class="npd">
<noscript>Please activate your JavaScript!</noscript>
<!-- ae_prompt HTML code -->
<div id="aep_ovrl" style="display: none;">&nbsp;</div>
<div id="aep_ww" style="display: none;">
    <div id="aep_win">
        <div id="aep_t"></div>
        <div id="aep_w">
            <span id="aep_prompt"></span>
            <br />
            <input type="text" id="aep_text" />
            <br />
            <div>
                <input type="hidden" id="call_function" >
                <input type="hidden" id="act_level">
                <input type="button" id="aep_ok" value="<?php echo addcslashes($npd->getLang('btn_ok'), '"'); ?>">
                <input type="button" id="aep_cancel" value="<?php echo addcslashes($lang['btn_cancel'], '"');?>">
            </div>
        </div>
    </div>
</div><!-- ae_prompt HTML code -->

<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div class="npd">
    <?php
    flush();
    /*old includehook*/
    @include(dirname(__FILE__).'/pageheader.html');
    ?>

    <div class="page" id="dw_page_div">
        <!-- wikipage start -->
        <?php tpl_content()?>
        <!-- wikipage stop -->
    </div>

    <div><?php echo $npd->getLang('dlg_path_info'); ?> /<span id="npd_show_path_info"></span> </div>
    <form action=''>
        <input type="hidden" class="" id="npd_ns" value="<?php echo trim($_REQUEST['idx'], ":"); ?>"/>
        <input type="text" class="text default" id="npd_page_name" value="<?php echo addcslashes($npd->getLang('msc_page_title'), '"'); ?>"
               class="default" onblur="if(this.value == '') { this.value = this.defaultValue; this.className = 'text default'; }"
               onfocus="if(this.value==this.defaultValue) {this.value=''; this.className = 'text';}"/>
        <input type="button" id="npd_save" class="text" value="<?php echo addcslashes($npd->getLang('btn_create_page'), '"');?>"/>
        <input type="button" id="npd_cancel" class="text" value="<?php echo addcslashes($lang['btn_cancel'], '"');?>"/>
        <input type="button" id="npd_new_folder" class="button" value="<?php echo addcslashes($npd->getLang('btn_new_folder'), '"')?>"/>
    </form>

    <div class="clearer"></div>
    <?php
    flush();
     /*old includehook*/ @include(dirname(__FILE__).'/footer.html');
    ?>
    <div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
</div>
</body>
</html>