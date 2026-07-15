@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])

@php
    // Logika Warna Premium (Inline) agar kompatibel di semua Email Client
    $bgColor = match($color) {
        'success' => '#10b981', // Emerald 500 (Hijau)
        'error'   => '#e11d48', // Rose 600 (Merah)
        default   => '#4f46e5', // Indigo 600 (Biru Utama SIAS)
    };
    
    // Warna Bayangan Halus
    $shadowColor = match($color) {
        'success' => 'rgba(16, 185, 129, 0.3)',
        'error'   => 'rgba(225, 29, 72, 0.3)',
        default   => 'rgba(79, 70, 229, 0.3)',
    };
@endphp

<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
{{-- Cell (TD) ini kita beri warna juga sebagai Bulletproof Fallback untuk Outlook PC --}}
<td style="border-radius: 12px; background-color: {{ $bgColor }}; box-shadow: 0 4px 14px {{ $shadowColor }};">
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener" 
   style="
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        display: inline-block; 
        padding: 14px 28px; 
        background-color: {{ $bgColor }}; 
        border-radius: 12px; 
        color: #ffffff; 
        font-size: 12px; 
        font-weight: 800; 
        letter-spacing: 1.5px; 
        text-transform: uppercase; 
        text-decoration: none; 
        border: 1px solid {{ $bgColor }};
   ">
    {!! $slot !!}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>