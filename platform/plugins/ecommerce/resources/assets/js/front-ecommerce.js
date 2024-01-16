class Ecommerce {
    /**
     * @returns {boolean}
     */
    isRtl() {
        return document.body.getAttribute('dir') === 'rtl'
    }

    /**
     * @param {JQuery} element
     */
    initLightGallery(element) {
        if (!element.length) {
            return
        }

        if (element.data('lightGallery')) {
            element.data('lightGallery').destroy(true)
        }

        element.lightGallery({
            selector: 'a',
            thumbnail: true,
            share: false,
            fullScreen: false,
            autoplay: false,
            autoplayControls: false,
            actualSize: false,
        })
    }

    initProductGallery() {
        const $gallery = $('.bb-product-gallery-images')
        const $thumbnails = $('.bb-product-thumbnails')

        if ($gallery.length) {
            if ($gallery.hasClass('slick-initialized')) {
                $gallery.slick('unslick')
            }

            $gallery.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                dots: false,
                infinite: false,
                fade: true,
                lazyLoad: 'ondemand',
                asNavFor: '.bb-product-thumbnails',
                rtl: this.isRtl(),
            })
        }

        if ($thumbnails.length) {
            if ($thumbnails.hasClass('slick-initialized')) {
                $thumbnails.slick('unslick')
            }

            $thumbnails.slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                arrows: true,
                asNavFor: '.bb-product-gallery-images',
                focusOnSelect: true,
                infinite: false,
                variableWidth: true,
                centerMode: true,
                rtl: this.isRtl(),
                prevArrow:
                    '<button class="slick-prev slick-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg></button>',
                nextArrow:
                    '<button class="slick-next slick-arrow"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg></button>',
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 4,
                        },
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3,
                        },
                    },
                ],
            })
        }

        this.initLightGallery($gallery)
    }
}

$(() => {
    window.EcommerceApp = new Ecommerce()
})
