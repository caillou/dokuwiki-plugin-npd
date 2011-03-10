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

if (plugin_isdisabled('npd') || !($npd =& plugin_load('helper', 'npd'))) {
    die();
}
$conf['template'] = $conf['template_original'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php tpl_pagetitle()?>
    [<?php echo strip_tags($conf['title'])?>]
  </title>

  <?php tpl_metaheaders()?>
<script type="text/javascript" language="javascript" charset="utf-8">
//<![CDATA[

// This is variable for storing callback function
var ae_cb = null;

// this is a simple function-shortcut
// to avoid using lengthy document.getElementById
function ae$(a) { return document.getElementById(a); }

// This is a main ae_prompt function
// it saves function callback
// and sets up dialog
function ae_prompt(cb, q, a) {
    ae_cb = cb;
    ae$('aep_t').innerHTML = '&nbsp;';//document.domain + ' question:';
    ae$('aep_prompt').innerHTML = q;
    ae$('aep_text').value = a;
    ae$('aep_ovrl').style.display = ae$('aep_ww').style.display = '';
    ae$('aep_text').focus();
    ae$('aep_text').select();
}

// This function is called when user presses OK(m=0) or Cancel(m=1) button
// in the dialog. You should not call this function directly.
function ae_clk(m) {
    // hide dialog layers
    ae$('aep_ovrl').style.display = ae$('aep_ww').style.display = 'none';
    if (!m)
        ae_cb(null);  // user pressed cancel, call callback with null
    else
        ae_cb(ae$('aep_text').value); // user pressed OK
}

new_folders = new Array();
addInitEvent(init_index);
function firstDescendant(element)
{
    element = element.firstChild;
    while (element && element.nodeType != 1) element = element.nextSibling;
    return element;
}
function getEventElement(e)
{
    if (typeof e.srcElement != 'undefined') {
        var node = e.srcElement;
    } else {
        var node = e.target;
    }
    if (node.nodeType == 3) {
        node = node.parentNode();
    }
    return node;
}
function npd_cancel(e)
{
    stop_event(e);
    window.close();
}
function npd_save(e)
{
    stop_event(e);
    var page_name = $('npd_page_name').value;
    var default_page_name = $('npd_page_name').defaultValue;
    if (page_name == default_page_name) {
        var answer = confirm('<?php echo htmlspecialchars($npd->getLang("dlg_confirm_page_name")); ?>'+page_name);
        if (!answer) return;
    }
    opener.location.href = "doku.php?do=edit&id=" + $('npd_ns').value + ":" + page_name;
    window.close();
}
function npd_new_folder(e)
{
    stop_event(e);
    ae_prompt(npd_new_folder_cb, "<?php echo str_replace(array("\n", "\r"), " ", $npd->locale_xhtml('dialog_folder_name')); ?>", "Untitled");
}
function npd_new_folder_cb(folder_name)
{
    if (! folder_name) {
        return;
    }
    folder_name = escapeHTML(folder_name).replace(/^\s*|\s*$/g,"");

    var node = active.parentNode;
    active.className = "idx_dir";

    node = node.nextSibling;
    while (node && node.nodeType != 1) node = node.nextSibling;

    if (!node) {
        // there is no UL yet, create it
        node = document.createElement("ul");
        node.className = 'idx';
        active.parentNode.parentNode.appendChild(node);
    }

    var npd_ns = $('npd_ns');
    var ns = npd_ns.value.replace(/:*$/,'') + ":" + folder_name;
    npd_ns.value = ns;

    // prepare new node
    var folder = document.createElement('li');
    folder.innerHTML = '<div class="li"><a class="idx_dir active" href="'+ns+'" ref="'+ns+'"><strong>' + folder_name + '</strong></a><a class="edit">[edit]</a></div><ul class="idx"/>';
    folder.className = 'new';

    child = firstDescendant(node);
    if (child) {
        node.insertBefore(folder, child);
    } else {
        node.appendChild(folder);
    }
    active = firstDescendant(firstDescendant(folder));
    addEvent(active, "click", new_folder_click);

    var edit = active.nextSibling;
    while (edit && edit.nodeType != 1) edit = edit.nextSibling;

    new_folders.push(active);

    addEvent(edit, "click", edit_folder_name);

}
function edit_folder_name(e)
{
    var link = getEventElement(e);
    if ((typeof link.href) == 'undefined') {
        // was a link clicked, or its content
        link = link.parentNode;
    }
    stop_event(e);
    folder = firstDescendant(link.parentNode);
    text_node = firstDescendant(folder);
    ae_prompt(edit_folder_name_cb, "<?php echo htmlspecialchars($npd->getLang('dlg_new_folder')); ?>", text_node.innerHTML);
}
function edit_folder_name_cb(response)
{
    if (!response) {
        return;
    }
    response = escapeHTML(response).replace(/^\s*|\s*$/g,"");;
    text_node.innerHTML = response;
    var old_ns = folder.getAttribute('ref');
    var new_ns = old_ns.replace(/:[^:]*$/, ":" + response);

    npd_ns = $('npd_ns');
    var regex = new RegExp("^" + old_ns);
    npd_ns.value = npd_ns.value.replace(regex, new_ns);
    var length = new_folders.length;
    if (length > 0) {
        while (length--) {
            var f = new_folders[length];
            console.log(new_ns);
            console.log(f.getAttribute('ref'));
            f.setAttribute('ref', f.getAttribute('ref').replace(regex, new_ns));
        }
    }
}

function new_folder_click(e)
{
    var link = getEventElement(e);
    stop_event(e);
    if (!link.getAttribute('ref')) {
        // was a link clicked, or its content
        link = link.parentNode;
    }
    active.className = 'idx_dir';
    $('npd_ns').value = link.getAttribute('ref');
    active = link;
    active.className = 'idx_dir active';
}
function page_click(e)
{
    link = getEventElement(e);
    stop_event(e);
    input = $('npd_page_name');
    input.value = link.innerHTML;
    input.className = 'text';
}
function plus_clicked(e)
{
    li = getEventElement(e);
    switch (li.nodeName.toLowerCase()) {
    	case "strong":
            li = li.parentNode.parentNode.parentNode;
            break;
        case "a":
            li = li.parentNode.parentNode;
            break;
    }
    if (li.className != 'closed') {
        return true;
    }
    window.location.href = firstDescendant(firstDescendant(li)).href;
}
function init_index()
{
    var div = $('dw_page_div');
    for (i=0; i<2; i++){
        var to_be_removed = firstDescendant(div);
        div.removeChild(to_be_removed);
    }
    links = document.getElementsByTagName("a");
    var pattern = new RegExp("(^|\\s)idx_dir(\\s|$)");
    var links_length = links.length;
    var li = '';
    for(i=0; i<links_length; i++) {
        if ( pattern.test(links[i].className) ) {
            links[i].href += '&npd=1';
            var a = links[i].href.replace(/.*idx=:?([^&]*).*/, "$1");
            var a = a.replace(/%3A/, ":");
            if (a == $('npd_ns').value) {
                links[i].className += " active";
                active = links[i];
            };
            li = links[i].parentNode.parentNode;
            if (li.className == "closed") {
                addEvent(li, "click", plus_clicked);
            }
        } else {
            addEvent(links[i], "click", page_click);
        }
    }
    // attach events to the buttons
    addEvent($('npd_save'), "click", npd_save);
    addEvent($('npd_cancel'), "click", npd_cancel);
    addEvent($('npd_new_folder'), "click", npd_new_folder);
    // add a root image
    // prepare new node
<?php
$title = addcslashes($conf['title'], "'");
if (! $title) {
    $title = 'Wiki';
}
?>
    var root = document.createElement('div');
    root.innerHTML = '<div class="li base"><a class="idx_dir" id="npd_root" href=":" ref=":"><strong><?php echo $title;?></strong></a></div>';
    root.className = 'base';
    var ul = firstDescendant(div);
    if (ul) {
        div.removeChild(ul);
    } else {
        ul = document.createElement("UL");
        ul.className = 'idx';
    }
    var root_link = firstDescendant(firstDescendant(root));
    var child = firstDescendant(div);
    if (child) {
        div.insertBefore(root, child);
    } else {
        div.appendChild(root);
    }
    root.appendChild(ul);
    addEvent(root_link, "click", new_folder_click);

    if ((typeof active) == 'undefined') {
        // in case the namespace the popup was called from
        // does not exist, we just make the root active
        active = root_link;
        active.className = "idx_dir active";
        $('npd_ns').value = "";
    }

    $('dw_page_div').style.display = '';
    $('npd_page_name').focus();
}
function stop_event(e)
{
    if (!!(window.attachEvent && !window.opera)){
        e.returnValue = false;
        e.cancelBubble = true;
    } else {
        e.preventDefault();
        e.stopPropagation();
    }
    e.stopped = true;
}
function escapeHTML(string) {
    var div = document.createElement('div');
    var text = document.createTextNode(string);
    div.appendChild(text);
    return div.innerHTML;
}
//]]>
</script>
<style type="text/css">
#aep_ovrl {
    background-color: black;
    -moz-opacity: 0.7; opacity: 0.7;
    top: 0; left: 0; position: fixed;
    width: 100%; height:100%; z-index: 99;
}
#aep_ww { position: fixed; z-index: 100; top: 0; left: 0; width: 100%; height: 100%; text-align: center;}
#aep_win { margin: 20% auto 0 auto; width: 400px; text-align: left;}
#aep_w {background-color: white; padding: 3px; border: 1px solid black; background-color: #EEE;}
#aep_t {color: white; margin: 0 0 2px 3px; font-family: Arial, sans-serif; font-size: 10pt;}
#aep_text {width:  98%;}
#aep_w span {font-family: Arial, sans-serif; font-size: 10pt;}
#aep_w div {text-align: right; margin-top: 5px;}
</style>
<!-- IE specific code: -->
<!--[if lte IE 7]>
<style type="text/css">
#aep_ovrl {
    position: absolute;
    filter:alpha(opacity=70);
    top: expression(eval(document.body.scrollTop));
    width: expression(eval(document.body.clientWidth));
}
#aep_ww {
    position: absolute;
    top: expression(eval(document.body.scrollTop));
}
</style>
<![endif]-->
  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />

  <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>
</head>

<body class="npd">
<!-- ae_prompt HTML code -->
<div id="aep_ovrl" style="display: none;">&nbsp;</div>
<div id="aep_ww" style="display: none;">
<div id="aep_win"><div id="aep_t"></div>
<div id="aep_w"><span id="aep_prompt"></span>
<br /><input type="text" id="aep_text" onKeyPress=
"if((event.keyCode==10)||(event.keyCode==13)) ae_clk(1); if (event.keyCode==27) ae_clk(0);">
<br><div><input type="button" id="aep_ok" onclick="ae_clk(1);" value="<?php echo addcslashes($npd->getLang('btn_ok'), '"'); ?>">
<input type="button" id="aep_cancel" onclick="ae_clk(0);" value="<?php echo addcslashes($lang['btn_cancel'], '"');?>">
</div></div>
</div>
</div>
<!-- ae_prompt HTML code -->
<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div class="npd">

  <?php flush()?>

  <?php /*old includehook*/ @include(dirname(__FILE__).'/pageheader.html')?>

  <div class="page" id="dw_page_div" style="display: none;">
    <!-- wikipage start -->
    <?php tpl_content()?>
    <!-- wikipage stop -->
  </div>
    <form action=''>
        <input type="text" class="" style="display: none;" id="npd_ns" value="<?php echo trim($_REQUEST['idx'], ":"); ?>"/>
        <input type="text" class="text default" id="npd_page_name"
               value="<?php echo addcslashes($npd->getLang('msc_page_title'), '"'); ?>"
               class="default"
               onblur="if(this.value == '') { this.value = this.defaultValue; this.className = 'text default'; }"
               onfocus="if(this.value==this.defaultValue) {this.value=''; this.className = 'text';}"/>
        <input type="button" id="npd_save" class="text" value="<?php echo addcslashes($npd->getLang('btn_create_page'), '"');?>"/>
        <input type="button" id="npd_cancel" class="text" value="<?php echo addcslashes($lang['btn_cancel'], '"');?>"/>
        <input type="button" id="npd_new_folder" class="button" value="<?php echo addcslashes($npd->getLang('btn_new_folder'), '"')?>"/>
    </form>

  <div class="clearer">&nbsp;</div>

  <?php flush()?>

<?php /*old includehook*/ @include(dirname(__FILE__).'/footer.html')?>

<div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
</div>
</body>
</html>
