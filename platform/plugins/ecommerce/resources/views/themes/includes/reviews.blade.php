@if (EcommerceHelper::isReviewEnabled())
    @php
        Theme::asset()->add('lightgallery-css', 'vendor/core/plugins/ecommerce/libraries/lightgallery/css/lightgallery.min.css');
        Theme::asset()->add('review-css', 'vendor/core/plugins/ecommerce/css/front-review.css');
        Theme::asset()->container('footer')->add('lightgallery-js', 'vendor/core/plugins/ecommerce/libraries/lightgallery/js/lightgallery.min.js', ['jquery']);
        Theme::asset()->container('footer')->add('lg-thumbnail-js', 'vendor/core/plugins/ecommerce/libraries/lightgallery/plugins/lg-thumbnail.min.js', ['lightgallery-js']);
        Theme::asset()->container('footer')->add('review-js', 'vendor/core/plugins/ecommerce/js/front-review.js', ['lightgallery-js', 'lg-thumbnail-js']);

        $showAvgRating ??= $product->reviews->isNotEmpty();
    @endphp

    <div class="d-flex flex-column gap-5 review-container">
        <div class="row">
            @if ($showAvgRating)
                <div class="col-12 col-md-4">
                    <div class="border py-4 px-4">
                        <h3 class="fs-1 text-danger">
                            {{ number_format($product->reviews_avg ?: 0, 2) }}
                        </h3>

                        <div class="d-flex align-items-center gap-1 border-bottom mb-3 pb-3">
                            <div class="rating-stars">
                                <span style="width: {{ $product->reviews_avg * 20 }}%"></span>
                            </div>
                            <span class="text-muted">({{ number_format($product->reviews_count) }})</span>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @foreach (EcommerceHelper::getReviewsGroupedByProductId($product->id, $product->reviews_count) as $item)
                                <div @class(['d-flex align-items-center justify-content-between w-100 gap-2', 'disabled' => ! $item['count']])>
                                    <span class="flex-shrink-0">
                                        @if($item['star'] == 1)
                                            {{ __(':number Star', ['number' => $item['star']]) }}
                                        @else
                                            {{ __(':number Stars', ['number' => $item['star']]) }}
                                        @endif
                                    </span>
                                    <div class="progress w-100">
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            aria-valuenow="{{ $item['percent'] }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            style="width: {{ $item['percent'] }}%"
                                        ></div>
                                    </div>
                                    <span class="flex-shrink-0">{{ $item['percent'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @include($reviewFormView ?? 'plugins/ecommerce::themes.includes.review-form')
        </div>

        @if (($reviewImagesCount = count($product->review_images)) > 0)
            <div class="review-images-container">
                <h4 class="mb-3">{{ __('Images from customer (:count)', ['count' => number_format($reviewImagesCount)]) }}</h4>

                <div class="row g-1 review-images">
                    @foreach ($product->review_images as $image)
                        <a href="{{ RvMedia::getImageUrl($image) }}" class="col-3 col-md-2 col-xl-1 position-relative" @style(['display: none !important' => $loop->iteration > 12])>
                            <img src="{{ RvMedia::getImageUrl($image, 'thumb') }}" alt="{{ $product->name }}" class="img-thumbnail">
                            @if ($loop->iteration === 12)
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75 rounded"></div>
                                <div class="position-absolute top-50 start-50 translate-middle text-white">
                                    <span class="badge bg-dark">+{{ __(':count more', ['count' => number_format($reviewImagesCount - 12)]) }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($product->reviews->isNotEmpty())
            <div class="position-relative review-list-container" data-ajax-url="{{ route('public.ajax.reviews', $product->id) }}">
                <h4 class="mb-3">{{ __(':total review(s) for ":product"', ['total' => number_format($product->reviews_count), 'product' => $product->name]) }}</h4>

                <div class="review-list"></div>
            </div>
        @else
            <p class="text-muted text-center">{{ __('Looks like there are no reviews yet.') }} </p>
        @endif
    </div>
@endif
