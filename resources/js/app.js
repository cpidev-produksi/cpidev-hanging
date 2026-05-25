import './bootstrap';
//import './inventory';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2/dist/css/select2.min.css';
import 'select2/dist/js/select2.min.js';

function initMonitorControlsSelect2() {
    const $farm = $('#farm_id');
    const $exp = $('#expedition_id');
    const $plate = $('#plate_number_id');

    if (!$farm.length || !$exp.length || !$plate.length) return;

    const SELECT2_COMMON = {
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 0,
    };

    $farm.select2($.extend({}, SELECT2_COMMON, {
        placeholder: 'Cari nama farm...',
        language: { noResults: () => 'Farm tidak ditemukan' }
    }));

    $exp.select2($.extend({}, SELECT2_COMMON, {
        placeholder: 'Cari nama ekspedisi...',
        language: { noResults: () => 'Ekspedisi tidak ditemukan' }
    }));

    // Simpan master data plate dari HTML (sekali)
    const masterPlate = [];
    $plate.find('option').each(function () {
        const val = $(this).val();
        if (!val) return;
        masterPlate.push({
            value: val,
            text: $(this).text(),
            expId: String($(this).attr('data-expedition') || '')
        });
    });

    function initPlateSelect2() {
        if ($plate.hasClass("select2-hidden-accessible")) {
            $plate.select2('destroy');
        }
        $plate.select2($.extend({}, SELECT2_COMMON, {
            placeholder: 'Cari nomor polisi...',
            language: { noResults: () => 'No polisi tidak ditemukan' }
        }));
    }

    function rebuildPlateOptions(expeditionId, restoreVal) {
        const eid = String(expeditionId || '');

        $plate.empty();
        $plate.append(new Option('— Pilih No Polisi —', '', false, false));

        if (!eid) {
            initPlateSelect2();
            $plate.val('').trigger('change');
            return;
        }

        masterPlate.forEach(d => {
            if (d.expId === eid) {
                $plate.append(new Option(d.text, d.value, false, false));
            }
        });

        initPlateSelect2();

        if (restoreVal && $plate.find('option[value="' + restoreVal + '"]').length) {
            $plate.val(restoreVal).trigger('change');
        } else {
            $plate.val('').trigger('change');
        }
    }

    $exp.on('change', function () {
        rebuildPlateOptions($(this).val(), '');
    });

    const oldExp = $plate.data('oldExpedition') || '';
    const oldPlate = $plate.data('oldPlate') || '';

    rebuildPlateOptions(oldExp, oldPlate);
}

document.addEventListener('DOMContentLoaded', initMonitorControlsSelect2);