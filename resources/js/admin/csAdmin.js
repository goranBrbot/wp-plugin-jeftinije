jQuery("#shoppersMindXmlUrl").on('click', function(e) {
    e.preventDefault();
    var urlPrefix = cenejeVars.xmlFeedUrl;
    var url = urlPrefix + jQuery('#ceneje_xml_url').val();

    const el = document.createElement('textarea');
    el.value = url;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);

});