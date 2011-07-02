/* "Very simple tooltips" by Marco Del Tongo <info@marcodeltongo.com> - Public Domain */
(function($){
    $.fn.extend({
        tip: function() {
            jQuery(this).unbind().hover(
                function(e) {
                    if (!this.title) return;
                    this.t = this.title;
                    this.title = '';
                    this.top = (e.pageY + 6);
                    this.left = (e.pageX - 6);

                    jQuery('body').append( '<p id="tip" class="ui-state-highlight ui-corner-all">' + this.t + '</p>' );
                    jQuery('p#tip').css("top", this.top+"px").css("left", this.left+"px").fadeIn(300);
                },
                function() {
                    if (!this.t) return;
                    this.title = this.t;
                    jQuery("p#tip").fadeOut(150, function() { jQuery(this).remove(); });
                }
            ).mousemove(
                function(e) {
                    jQuery("p#tip").css("top", (e.pageY + 6)+"px").css("left", (e.pageX - 6)+"px");
                }
            );
        }
    });
})(jQuery);