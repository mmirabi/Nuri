@php
    Theme::layout('full-width');
@endphp

<div class="page-content pt-150 pb-150">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                <div class="row justify-content-md-center">
                    <div class="col-lg-8 col-md-8">
                        <div class="login_wrap widget-taber-content background-white">
                            <div class="padding_eight_all verify-page">
                                <div class="heading_s1">
                                    <h1 class="mb-5">{{ __('Verify SMS') }}</h1>
                                    <p class="mb-30">{{ __("please enter verification code") }} </p>
                                </div>

{{--                                <h1 class="title-login-register">{{ __('Login With Phone') }}</h1>--}}
                                <form method="POST" action="{{ route('customer.verify') }}">
                                    @csrf
                                    @if (isset($errors) && $errors->has('confirmation'))
                                        <div class="alert alert-danger">
                                            <span>{!! $errors->first('confirmation') !!}</span>
                                        </div>
                                        <br>
                                    @endif
                                    <div class="form-group">
                                        <input name="verify-code" class="verify-code" required id="verify-code" type="number" value="{{ old('verify-code') }}" placeholder="{{ __('Verify Code') }}*">
                                        @if ($errors->has('verify-code'))
                                            <span class="text-danger">{{ $errors->first('verify-code') }}</span>
                                        @endif
                                    </div>
                                    <div class="timer-2min"><p>{{ __('Left Time')  }}</p> <span id="timer"></span></div>
                                    <div id="sendCode">
                                        <button id="sendCodeButton" type="submit" class="btn btn-heading btn-block send-code">{{ __('Send again') }}</button>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-heading btn-block">{{ __('Verify') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
<script src="{{ Theme::asset()->url('js/custom.js') }}"></script>
