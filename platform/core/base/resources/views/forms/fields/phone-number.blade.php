@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
    @endif
@endif

@if ($showLabel && $options['label'] !== false && $options['label_show'])
    {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif

@if ($showField)
    @if ($prepend = Arr::get($options, 'prepend'))
        {!! $prepend !!}
    @endif

    {!! Form::text(
        $name,
        $options['value'],
        array_merge_recursive($options['attr'], ['class' => 'js-phone-number-mask form-control']),
    ) !!}

    @if ($append = Arr::get($options, 'append'))
        {!! $append !!}
    @endif

    @include('core/base::forms.partials.help-block')
@endif

@include('core/base::forms.partials.errors')

@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
