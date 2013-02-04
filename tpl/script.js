function firstDescendant(element) {
    element = element.firstChild;
    while (element && element.nodeType != 1) element = element.nextSibling;
    return element;
}

jQuery(function () {

    var pages = null; // saves an array of pages
    var edit_object = null;

    init();
    function init() {

        var div = $('dw_page_div');
        // removes the first and the second headline
        for (var i = 0; i < 2; i++) {
            div.removeChild(firstDescendant(div));
        }

        // add a root image
        // prepare new node
        var root = document.createElement('div');
        root.innerHTML = '<div class="li base"><a class="idx_dir" id="npd_root" href=":" ref=":"><strong>' + LANG.plugins.npd['wiki_title'] + '</strong></a></div>';
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

        if ((typeof active) == 'undefined') {
            // in case the namespace the popup was called from
            // does not exist, we just make the root active
            active = root_link;
            active.className = "idx_dir active";
            $('npd_ns').value = "";
        }

        $('npd_page_name').focus();
    }

    jQuery("#npd_save").click(function (e) {
        var page_name = jQuery('#npd_page_name').val();

        if (page_name === "") {
            // no action
            e.preventDefault();
            return false;
        }

        // if selected name is default pagename
        if (page_name === LANG.plugins.npd['msc_page_title']) {
            var answer = confirm(LANG.plugins.npd["dlg_confirm_page_name"] + " " + page_name);
            if (!answer) {
                // no action
                e.preventDefault();
                return false;
            }
        }
        opener.location.href = "doku.php?do=edit&id=" + jQuery('#npd_ns').val() + page_name;
        window.close();
    });

    jQuery("#npd_cancel").click(function () {
        window.close();
    });

    jQuery("#npd_new_folder").click(function () {
        jQuery('#aep_prompt').empty();
        jQuery('#aep_prompt').append(LANG.plugins.npd['dialog_folder_name']);
        jQuery("#aep_ovrl").show();
        jQuery("#aep_ww").show();
        jQuery("#aep_text").select();
        jQuery('#call_function').val('new_folder');
    });

    jQuery("#aep_ok").live("click", function (e) {

        // get foldername and remove whitespaces
        var folder_name = jQuery.trim(jQuery('#aep_text').val());

        // if foldername is empty
        if (folder_name === "") {
            // no action
            e.preventDefault();
            return false;
        }

        // hide dialog layers
        document.getElementById('aep_ovrl').style.display = document.getElementById('aep_ww').style.display = 'none';

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

        var ancestors = jQuery("#npd_ns").val();
        var path_info = "";

        // values could be edit or new_folder
        var call_function = jQuery("#call_function").val();
        switch (call_function) {
            case "edit":
                if (edit_object !== null) {
                    var act_index = jQuery("#act_level").val() - 1;
                    var ancestor_array = ancestors.split(":");
                    var ancestor_array_length = ancestor_array.length;

                    // build compare_path, this will be used for change the title in subfolders
                    var compare_path = "";
                    for (var i=0; i<=act_index; i++) {
                        compare_path = compare_path.concat(ancestor_array[i]).concat(":");
                    }
                    // put new name and build path
                    ancestor_array[act_index] = folder_name;
                    for (var i = 0; i <= act_index; i++) {
                        if (ancestor_array[i] !== "") {
                            if (i < act_index) {
                                path_info = path_info.concat(ancestor_array[i]).concat(":");
                            } else {
                                path_info = path_info.concat(ancestor_array[i]);
                            }
                        }
                    }
                    var text = "<strong>".concat(folder_name).concat("</strong>");
                    edit_object.empty();
                    edit_object.append(text);
                    edit_object.attr("title", path_info);
                    edit_object.attr("href", 'href="doku.php?idx='+path_info+'"');

                    // edit the name also in subfolders
                    // in pages it is not neccessary because there are no pages in a new folder
                    var folders = jQuery('.idx_dir');
                    var folder_length = folders.length;

                    for (var i = 0; i < folder_length; i++) {
                        if (typeof(folders.eq(i).attr('title')) === "undefined") {
                            continue;
                        }
                        if (folders.eq(i).attr('title').indexOf(compare_path) >= 0) {
                            var new_title = folders.eq(i).attr('title').replace(compare_path, path_info.concat(":"));
                            folders.eq(i).attr('title', new_title);
                            folders.eq(i).attr('href', "doku.php?idx=" + new_title);
                        }
                    }

                }
                break;
            case "new_folder":
                path_info = (ancestors.concat(folder_name));

                // get the parent folder and append the new folder
                var parent = null;
                jQuery("a").each(function () {
                    var actLink = (jQuery(this).attr('title'));
                    // maybe undefined
                    if (typeof(actLink) != "undefined") {
                        actLink = actLink.concat(":")
                        if (actLink === ancestors) {
                            parent = this;
                        }
                    }
                });

                var new_folder;
                // directory will created into root directory
                if (parent === null) {
                    parent = jQuery("#index__tree ul:first-child");
                    new_folder = '<li class="open" id="new"><div class="li"><a class="idx_dir active" title="' + path_info + '" href="doku.php?idx=' + path_info +'"><strong>' + folder_name + '</strong></a><a class="edit">[edit]</a></div></li>';
                    jQuery(parent).prepend(new_folder);
                } else {
                    var nextExists = (jQuery(parent).parent().next("ul").text() !== "") ? true : false;
                    if (nextExists) {
                        new_folder = '<li class="open" id="new"><div class="li"><a class="idx_dir active" href="doku.php?idx=' + path_info +'" title="' + path_info + '"><strong>' + folder_name + '</strong></a><a class="edit">[edit]</a></div></li>';
                        jQuery(parent).parent().next("ul").prepend(new_folder);
                    } else {
                        new_folder = '<ul class="idx"><li class="open" id="new"><div class="li"><a class="idx_dir active" title="' + path_info + '" href="doku.php?idx=' + path_info +'"><strong>' + folder_name + '</strong></a><a class="edit">[edit]</a></div></li></ul>';
                        jQuery(parent).parent().after(new_folder);
                    }
                }
                break;
        }
        path_info = path_info.concat(":");

        // set value into the hidden field
        jQuery("#npd_ns").val(path_info);

        // set value into visible information field
        path_info = path_info.replace(/:/g, "/"); // replace all colons
        jQuery("#npd_show_path_info").empty();
        jQuery("#npd_show_path_info").append(path_info);
    });

    jQuery(".edit").live("click", function () {
        var folder_name = jQuery(this).prev().text();
        if (typeof(folder_name !== "undefined")) {
            jQuery('#aep_prompt').empty();
            jQuery('#aep_prompt').append(LANG.plugins.npd['dlg_new_folder']);
            jQuery('#aep_text').val(folder_name);
            jQuery("#aep_ovrl").show();
            jQuery("#aep_ww").show();
            jQuery("#aep_text").select();
            jQuery('#call_function').val('edit');
            jQuery('#act_level').val(((jQuery(this).prev().attr('title')).split(":")).length);
            edit_object = jQuery(this).prev();

        }
    });

    jQuery("#aep_cancel").live("click", function () {
        jQuery("#aep_ovrl").hide();
        jQuery("#aep_ww").hide();
    });

    jQuery(".wikilink1").live("click", function (e) {

        // the beginning for this title are the namespace, e.g. namespace1:namespace2:mydocument
        var title = (jQuery(this).attr('title')).split(":");
        var page_name, path_info = "";
        for (var i = 0; i < title.length; i++) {
            if (i == (title.length - 1)) {
                page_name = title[i];
            } else {
                if (i == (title.length - 2)) {
                    path_info = path_info.concat((title[i]));
                } else {
                    path_info = path_info.concat((title[i]).concat(":"));
                }
            }
        }

        if (path_info === "") {
            jQuery("#npd_ns").val("");
            jQuery("#npd_show_path_info").empty();
        } else {
            // set value into hidden field
            jQuery("#npd_ns").val(path_info + ":");

            // set value into visible information field
            path_info = path_info.replace(/:/g, "/"); // replace all colons
            jQuery("#npd_show_path_info").empty()
            jQuery("#npd_show_path_info").append(path_info);
        }

        jQuery('#npd_page_name').val(page_name);
        jQuery('#npd_save').val(LANG.plugins.npd['btn_edit_page']);

        // the link is not followed
        e.preventDefault();
        return false;
    });

    jQuery("#npd_root").live("click", function (e) {

        jQuery("#npd_ns").val("");
        jQuery("#npd_show_path_info").empty();
        jQuery('#npd_page_name').val("");
        jQuery('#npd_save').val(LANG.plugins.npd['btn_create_page']);

        // the link is not followed
        e.preventDefault();
        return false;
    });

    jQuery("#npd_page_name").keyup(function (e) {
        if (pages == null) {
            pages = jQuery('.wikilink1');
        }
        var pagesLength = pages.length;
        var act_input = jQuery("#npd_ns").val().concat(jQuery("#npd_page_name").val());

        for (var i = 0; i < pagesLength; i++) {
            if (pages.eq(i).attr('title') == act_input) {
                jQuery('#npd_save').val(LANG.plugins.npd['btn_edit_page']);
                e.preventDefault();
                return false;
            } else {
                jQuery('#npd_save').val(LANG.plugins.npd['btn_create_page']);
            }
        }
    });

    jQuery(".idx_dir").live("click", function () {
        var path_info = (jQuery(this).attr('title')).concat(":");

        // set value into hidden field
        jQuery("#npd_ns").val(path_info);

        // set value into visible information field
        path_info = path_info.replace(/:/g, "/"); // replace all colons
        jQuery("#npd_show_path_info").empty();
        jQuery("#npd_show_path_info").append(path_info);

        // delete actual page_name, because folder is selected
        jQuery("#npd_page_name").val("");
        jQuery('#npd_save').val(LANG.plugins.npd['btn_create_page']);
    });

});