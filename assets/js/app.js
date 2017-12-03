(function ($) {
    if (!$) {
        return;
    }
    $(document).ready(function () {
        // on ready
        $('#sidebar .navigation > ul > li a').on('click', function (e) {
            var $this = $(this),
                $parent = $this.parent(),
                $ul = $parent.find('> ul');
            if (!$ul.length) {
                return;
            }
            e.preventDefault();
            $ul.toggleClass('active');
        })
    });
})(window.jQuery);