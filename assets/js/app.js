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
        });
        if ($.fn.autocomplete && typeof api_url === 'object') {
            $('[data-autocomplete]').each(function() {
                var $this = $(this);
                var type = $(this).attr('data-autocomplete');
                var selector;
                switch (type) {
                    case 'pengarang':
                        selector = 'pengarang';
                        break;
                    case 'anggota':
                        selector = 'user_name';
                        break;
                    case 'judul':
                    case 'buku_pengarang':
                        selector = 'judul';
                        break;
                }
                if (type && typeof api_url[type] === 'string') {
                    var options = {
                        serviceUrl: function(phrase) {
                            return api_url[type] + '/' + phrase;
                        },
                        dataType: 'json',
                        deferRequestBy: 1000,
                        paramName: 'data',
                        transformResult: function(response) {
                            return {
                                suggestions: $.map(response.data, function(dataItem) {
                                    return { value: dataItem[selector], data: dataItem };
                                })
                            };
                        }
                    };
                    try {
                        $this.autocomplete(options);
                    } catch (err) {
                        console.log(err);
                    }
                }
            });
        }
    });
})(window.jQuery);