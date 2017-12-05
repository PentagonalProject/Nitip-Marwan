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
        if ($.fn.wysihtml5) {
            $('textarea').wysihtml5({image: false});
        }
        /**
         * Pengubah Tanggal
         * @type {*|jQuery|HTMLElement}
         */
        var $tahun = $('select[id*=tahun]');
            $tahun.each(function () {
                try {
                    var $this = $(this),
                        year_selected = $this.find(':selected').val() || new Date().getFullYear(),
                        selector_tahun_id = $this.attr('id'),
                        selector = selector_tahun_id.replace(/-[^\-]+$/, ''),
                        $tanggal = $('select#'+selector+'-tanggal'),
                        $bulan = $('#'+selector+'-bulan');
                    if ($tanggal.length && $bulan.length) {
                        var selected_tanggal_old = $tanggal.find(':selected').val() || 1;
                        var selected_bulan = $bulan.find(':selected').val() || 1;
                        var selected_tanggal = selected_tanggal_old;
                        var changeTanggalList = function (max) {
                            selected_tanggal = selected_tanggal || selected_tanggal_old;
                            selected_tanggal = parseInt(selected_tanggal);
                            selected_tanggal = max > selected_tanggal ? selected_tanggal_old: selected_tanggal;
                            selected_tanggal = selected_tanggal < 1 ? 1 : selected_tanggal;
                            selected_tanggal = parseInt(selected_tanggal);
                            var selected = '',
                                index = 0,
                                val = '';
                            for (var i = 0; max > i;i++) {
                                index = i+1;
                                selected  = selected_tanggal === index  ? ' selected' : '';
                                val += '<option value="' + index + '"' + selected + '>' + index +'</option>\n';
                            }
                            $tanggal.html(val);
                        };
                        $this.on('change', function () {
                            year_selected = $(this).find(':selected').val() || new Date().getFullYear();
                            var dates = new Date(year_selected, selected_bulan, 0);
                                dates = parseInt(dates.getDate());
                            changeTanggalList(dates);
                        });
                        $bulan.on('change', function () {
                            selected_bulan = $(this).find(':selected').val() || 1;
                            var dates = new Date(year_selected, selected_bulan, 0);
                                dates = parseInt(dates.getDate());
                            changeTanggalList(dates);
                        });
                        $tanggal.on('change', function () {
                            selected_tanggal = $(this).find(':selected').val() || selected_tanggal_old;
                        })
                    }
                } catch (err) {
                    console.error(err);
                    // pass
                }
            });

        if ($.fn.select2 && typeof api_url === 'object') {
            $('select[data-select=select2]').each(function () {
                $(this).select2();
            });
            function pengarang_penerbit() {
                var $this = $(this);
                var type  = $this.attr('name').match(/pengarang/) ? 'pengarang' : 'penerbit';
                if (typeof api_url[type] !== 'string') {
                    return;
                }
                var selectp = $('<br/><p><em><small>Gunakan select di bawah untuk mencari ' + type +':</small></em><br/></p>');
                var select2new =  $('<select class="form-control"></select>');
                var select2newOption = $('<option selected>'+$this.val()+'</option>');
                select2newOption.attr('value', $this.val());
                select2new.html(select2newOption);
                selectp.append(select2new);
                select2new.on('change', function () {
                    $this.val($(this).find(':selected').text());
                });
                $this.parent('td').prev('td').attr('style', 'vertical-align: top');
                select2new.select2({
                    width: "100%",
                    placeholder: 'Pilih ' + type,
                    allowClear: false,
                    ajax: {
                        minimumInputLength: 2,
                        delay: 250,
                        url: function (params) {
                            var r = params.term || '';
                            r = r.replace(/^\s+/, '').replace(/\s+$/, '');
                            r = r !== '' ? '/' + r : '';
                            return api_url[type].replace(/\/+$/, '') + r;
                        },
                        cache:true,
                        method: "GET",
                        processResults: function (data) {
                            var result = [];
                            $.map(data.data, function (data) {
                                result.push({
                                    text: data,
                                    id: data
                                });
                            });
                            // Tranforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: result
                            };
                        }
                    }
                });
                $this.after(selectp);
            }
            /**
             * Pengarang completion
             */
            $('input[name=buku\\[pengarang\\]]').each(pengarang_penerbit);
            $('input[name=buku\\[penerbit\\]]').each(pengarang_penerbit);
            $('select[name=buku\\[anggota_id_pinjam\\]]').each(function () {
                var $this = $(this);
                if (typeof api_url['anggota'] !== 'string') {
                    return;
                }
                $this.select2({
                    width: "100%",
                    placeholder: 'Pilih User Name',
                    allowClear: false,
                    ajax: {
                        minimumInputLength: 2,
                        delay: 250,
                        url: function (params) {
                            var r = params.term || '';
                            r = r.replace(/^\s+/, '').replace(/\s+$/, '');
                            r = r !== '' ? '/' + r : '';
                            return api_url['anggota'].replace(/\/+$/, '') + r;
                        },
                        cache:true,
                        method: "GET",
                        processResults: function (data) {
                            // console.log(data);
                            // throw new Error();
                            var result = [
                                {
                                    text: "Tidak Terpinjam",
                                    id: 0
                                }
                            ];
                            $.map(data.data, function (data) {
                                result.push({
                                    text: data['user_name'] + (data['is_admin'] ? ' - [admin]' : ''),
                                    id: data.id
                                });
                            });
                            // Tranforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: result
                            };
                        }
                    }
                });
            });
        }
    });
})(window.jQuery);