@php
    /**
     * @var \App\Languages\Models\Language $language
     */
@endphp

<li class="nav-item">
    <a class="nav-link" href="#page-variant-{{ $language->id }}" role="tab" data-toggle="tab" tabindex="-1">
        {{ $language->english_name }}
    </a>
</li>
