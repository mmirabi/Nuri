<div class="mb-3">
    <label for="widget-name">{{ __('Name') }}</label>
    <input type="text" id="widget-name" class="form-control" name="name" value="{{ $config['name'] }}">
</div>

<div style="max-height: 400px; overflow: auto" class="border">
    @for ($i = 1; $i <= 5; $i++)
        <div class="bg-light p-1">
            <div class="form-group mb-3">
                <label>{{ __('Title :number', ['number' => $i]) }}</label>
                <input type="text" class="form-control" name="data[{{ $i }}][title]" value="{{ Arr::get(Arr::get($config['data'], $i), 'title') }}">
            </div>
            <div class="form-group mb-3">
                <label>{{ __('Subtitle :number', ['number' => $i]) }}</label>
                <input type="text" class="form-control" name="data[{{ $i }}][subtitle]" value="{{ Arr::get(Arr::get($config['data'], $i), 'subtitle') }}">
            </div>
            <div class="form-group mb-3">
                <label>{{ __('Icon :number', ['number' => $i]) }}</label>
                {!! Form::mediaImage('data[' . $i . '][icon]', Arr::get(Arr::get($config['data'], $i), 'icon')) !!}
            </div>
        </div>
    @endfor
</div>

