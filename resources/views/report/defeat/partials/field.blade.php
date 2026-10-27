{{--
    Satu input angka pada form farm.
    Wajib : $key, $f (definisi dari DefeatReportItem::FIELDS), $value (nilai awal)
    Opsi  : $unit (satuan di dalam kolom), $placeholder, $invalid (bool), $disabled (bool), $qc (qc-in marker)
--}}
@php
    $cls = $f['kind'] === 'time' ? 'mmss' : ($f['kind'] === 'int' ? 'int' : 'dec');
    $unit = $unit ?? null;
@endphp
<div class="fld">
    <label for="f_{{ $key }}">{{ $label ?? $f['label'] }}</label>
    <div class="inp{{ $unit ? ' has-unit' : '' }}">
        <input type="text" id="f_{{ $key }}" name="{{ $key }}"
               class="num {{ $cls }}{{ ! empty($qc) ? ' qc-in' : '' }}{{ ! empty($invalid) ? ' is-invalid' : '' }}"
               data-key="{{ $key }}" data-kind="{{ $f['kind'] }}"
               @if($f['max'] !== null) data-max="{{ $f['max'] }}" @endif
               @if(! empty($f['hist'])) data-hist="1" @endif
               @if($f['kind'] === 'time') maxlength="5" @endif
               inputmode="{{ $f['kind'] === 'dec' || $f['kind'] === 'time' ? 'decimal' : 'numeric' }}"
               placeholder="{{ $placeholder ?? '' }}"
               value="{{ $value }}"
               @if($f['req']) required @endif
               @if(! empty($disabled)) disabled @endif>
        @if($unit)<span class="unit">{{ $unit }}</span>@endif
    </div>
</div>
