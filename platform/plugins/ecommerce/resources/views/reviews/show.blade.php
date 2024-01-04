@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row" id="review-section-wrapper">
        <div class="col-md-8 mb-3 mb-md-0">
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <h4 class="card-title d-flex justify-content-between align-items-center w-100">
                        @include('plugins/ecommerce::reviews.partials.rating', [
                            'star' => $review->star,
                        ])
                        {!! BaseHelper::clean($review->status->toHtml()) !!}
                    </h4>
                </x-core::card.header>
                <x-core::card.body>
                    <p class="card-text">
                        {{ $review->comment }}
                    </p>

                    <div class="d-flex justify-content-end">
                        <div class="btn-list">
                            <x-core::button
                                color="danger"
                                :outlined="true"
                                data-bb-toggle="review-delete"
                                data-target="{{ route('reviews.destroy', $review) }}"
                            >
                                {{ trans('plugins/ecommerce::review.delete') }}
                            </x-core::button>
                            @if ($review->status == Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                                <x-core::button
                                    color="warning"
                                    data-id="{{ $review->getKey() }}"
                                    data-bb-toggle="review-unpublish"
                                >{{ trans('plugins/ecommerce::review.unpublish') }}</x-core::button>
                            @else
                                <x-core::button
                                    color="primary"
                                    data-id="{{ $review->getKey() }}"
                                    data-bb-toggle="review-publish"
                                >{{ trans('plugins/ecommerce::review.publish') }}</x-core::button>
                            @endif
                        </div>
                    </div>
                </x-core::card.body>

                <x-core::card.footer class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        {{ $review->user->name }}
                        (<a href="mailto:{{ $review->user->email }}">{{ $review->user->email }}</a>)
                    </div>
                    <div>
                        {{ $review->created_at->diffForHumans() }}
                    </div>
                </x-core::card.footer>
            </x-core::card>

            @if (auth()->user()->hasPermission('reviews.reply') || $review->reply)
                <x-core::card>
                    <x-core::card.header>
                        <x-core::card.title>
                            {{ trans('plugins/ecommerce::review.reply_to_review') }}
                        </x-core::card.title>
                    </x-core::card.header>
                    <x-core::card.body>
                        @if (auth()->user()->hasPermission('reviews.reply'))
                            <x-core::form :url="$review->reply ? route('reviews.reply.update', [$review, $review->reply]) : route('reviews.reply', $review)" :method="$review->reply ? 'PUT' : 'POST'">
                                <div class="mb-3">
                                    <x-core::form.textarea
                                        name="message"
                                        :placeholder="trans('plugins/ecommerce::review.write_your_reply')"
                                        :value="old('message', $review->reply ? $review->reply->message : '')"
                                    />
                                </div>

                                <x-core::button
                                    type="submit"
                                    color="primary"
                                    icon="ti ti-send"
                                >
                                    @if ($review->reply)
                                        {{ trans('core/base::forms.update') }}
                                    @else
                                        {{ trans('plugins/ecommerce::review.reply') }}
                                    @endif
                                </x-core::button>
                            </x-core::form>
                        @else
                            {{ $review->reply->message }}
                        @endif
                    </x-core::card.body>
                    @if ($review->reply)
                        <x-core::card.footer>
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    {{ $review->reply->user->name }}
                                    (<a href="mailto:{{ $review->reply->user->email }}">{{ $review->reply->user->email }}</a>)
                                </div>
                                <div>
                                    {{ $review->reply->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </x-core::card.footer>
                    @endif
                </x-core::card>
            @endif
        </div>
        <div class="col-md-4">
            <x-core::card>
                <x-core::card.header>
                    <h4 class="card-title">
                        {{ trans('plugins/ecommerce::review.product') }}
                    </h4>
                </x-core::card.header>

                <x-core::card.body>
                    <div class="d-flex gap-3 align-items-start">
                        <img
                            class="img-thumbnail"
                            src="{{ RvMedia::getImageUrl($review->product->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                            alt="{{ $review->product->name }}"
                            style="width: 15%"
                        >
                        <div>
                            <h4>
                                <a href="{{ route('products.edit', $review->product) }}">
                                    {{ $review->product->name }}
                                </a>
                            </h4>
                            <div>
                                @include('plugins/ecommerce::reviews.partials.rating', [
                                    'star' => $review->product->reviews_avg_star,
                                ])
                                <span>({{ number_format($review->product->reviews_count) }})</span>
                            </div>
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
@endsection

@push('footer')
    <x-core::modal.action
        type="danger"
        id="delete-review-modal"
        :title="trans('plugins/ecommerce::review.delete_modal.title')"
        :description="trans('plugins/ecommerce::review.delete_modal.description')"
        :submit-button-label="trans('plugins/ecommerce::review.delete')"
        :submit-button-attrs="['id' => 'confirm-delete-review-button']"
    />
@endpush
