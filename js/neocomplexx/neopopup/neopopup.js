(function($) {
    $(document).ready(function(){
        var delayMillis = 1000; //1 second
        setTimeout(function() {
            console.log("Showing newsletter modal");
            $("#neocomplexx_modal_newsletter").modal();
        }, delayMillis);
    });
})(jQuery);