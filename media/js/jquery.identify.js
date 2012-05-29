jQuery.fn.identify = function(prefix) {
    var i = 0;
    return this.each(function() {
        if(jQuery(this).attr('id')) return;
        do { 
            i++;
            var id = (prefix || '__auto_id_') + '_' + i;
        } while(jQuery('#' + id).length > 0);            
        jQuery(this).attr('id', id);            
    });
};