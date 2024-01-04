@push('meta-box-header-seo_wrap')
    <x-core::card.actions>
        <a href="#" class="btn-trigger-show-seo-detail">
            {{ trans('packages/seo-helper::seo-helper.edit_seo_meta') }}
        </a>
    </x-core::card.actions>
@endpush

<div
    @class(['seo-preview', 'noindex' => $meta['index'] === 'noindex'])
    v-pre
>
    <p @class(['default-seo-description', 'hidden' => !empty($object->id)])>
        {{ trans('packages/seo-helper::seo-helper.default_description') }}
    </p>

    <div @class(['existed-seo-meta', 'hidden' => empty($object->id)])>
        @if ($meta['index'] === 'noindex')
            <span class="page-index-status">
                <x-core::icon name="ti ti-search-off" class="text-warning" size="sm" />

                {{ trans('packages/seo-helper::seo-helper.noindex') }}
            </span>
        @endif

        <h4 class="page-title-seo text-truncate">
            {!! BaseHelper::clean($meta['seo_title'] ?? (!empty($object->id) ? $object->name ?? $object->title : null)) !!}
        </h4>

        <div class="page-url-seo">
            <p>{{ !empty($object->id) && $object->url ? $object->url : '-' }}</p>
        </div>

        <div>
            <span style="color: #70757a;">{{ !empty($object->id) && $object->created_at ? $object->created_at->format('M d, Y') : Carbon\Carbon::now()->format('M d, Y') }}
                - </span>
            <span class="page-description-seo">
                @if (!empty($meta['seo_description']))
                    {{ strip_tags($meta['seo_description']) }}
                @elseif ($metaDescription = (!empty($object->id) ? ($object->description ?: ($object->content ? Str::limit($object->content, 250) : old('seo_meta.seo_description'))) : old('seo_meta.seo_description')))
                    {{ strip_tags($metaDescription) }}
                @endif
            </span>
        </div>
    </div>
</div>

<div
    class="hidden seo-edit-section"
    v-pre
>
    <x-core::hr class="my-4" />

    {!! $form !!}
</div>
