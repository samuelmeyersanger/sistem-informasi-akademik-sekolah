<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
    {{-- Teks Header yang Sangat Elegan dan Bold --}}
    <div style="margin-top: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-weight: 900; color: #0f172a; font-size: 18px; letter-spacing: 3px; text-transform: uppercase;">
        {{ config('app.name') }}
    </div>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy (Link Cadangan) --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
    {{-- Hak Cipta Dinamis yang Dirapikan --}}
    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 10px; color: #64748b; line-height: 1.8; text-align: center; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">
        &copy; {{ date('Y') }} <span style="color: #0f172a; font-weight: 900;">{{ config('app.name') }}</span>.<br>
        @lang('All rights reserved.')
    </p>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>