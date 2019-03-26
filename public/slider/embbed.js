$(document).ready(function(){
    if($sliderId = $('#shopmacher-slider').attr('slider-id')) {
        $('<script src="https://8a80ba3a.ngrok.io/slider/lib.js?slider-id='+$sliderId+'"></script>').appendTo($('body'));
    }
});
