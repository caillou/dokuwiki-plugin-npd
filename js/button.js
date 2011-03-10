addInitEvent(init_npd);
function init_npd()
{
    var button = $('npd_create_button');
    if (!button) return;
    
    if (button.nodeName.toLowerCase() == 'a') {
        npd_clicked_url = button.getAttribute('href');
    } else {
        npd_clicked_url = button.parentNode.parentNode.getAttribute('action');
    }
    
    addEvent(button, "click", npd_clicked);

    // show the button
    button.style.display = '';
}

function npd_clicked(e)
{
    // the event is extended by the eventhandler,
    // so the following works in IE:
    e.preventDefault();
    e.stopPropagation();
    e.stopped = true;
    // create dialog
    w = window.open(npd_clicked_url, "", "width=753,height=400,resizable=no"); 
    w.focus();
}
