{{--
    Satu input angka pada form farm Efficiency Evisceration.
    Wajib : $key, $label, $value
    Opsi  : $kind (int|dec, default int), $max, $unit, $placeholder, $invalid, $autofill (nama kunci data Defeathering), $attrs
--}}
@php
    $kind = $kind ?? 'int';
    $unit = $unit ?? null;
@endphp
<div class="fld">
    <label for="f_{{ $key }}">{{ $label }}</label>
    <div class="inp{{ $unit ? ' has-unit' : '' }}">
        <input type="text" id="f_{{ $key }}" name="{{ $key }}"
               class="num {{ $kind }}{{ ! empty($invalid) ? ' is-invalid' : '' }}"
               inputmode="{{ $kind === 'dec' ? 'decimal' : 'numeric' }}"
               @if(isset($max)) data-max="{{ $max }}" @endif
               @if(! empty($autofill)) data-autofill="{{ $autofill }}" @endif
               placeholder="{{ $placeholder ?? '' }}"
               value="{{ $value }}" required>
        @if($unit)<span class="unit">{{ $unit }}</span>@endif
    </div>
</div>