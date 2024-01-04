<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\Wishlist;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WishlistController extends BaseController
{
    public function index(Request $request, ProductInterface $productRepository)
    {
        if (! EcommerceHelper::isWishlistEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Wishlist'));

        $queryParams = array_merge([
            'paginate' => [
                'per_page' => 10,
                'current_paged' => $request->integer('page', 1) ?: 1,
            ],
            'with' => ['slugable'],
        ], EcommerceHelper::withReviewsParams());

        if (auth('customer')->check()) {
            $products = $productRepository->getProductsWishlist(auth('customer')->id(), $queryParams);
        } else {
            $products = new LengthAwarePaginator([], 0, 10);

            $itemIds = collect(Cart::instance('wishlist')->content())
                ->sortBy([['updated_at', 'desc']])
                ->pluck('id')
                ->all();

            if ($itemIds) {
                $products = $productRepository->getProductsByIds($itemIds, $queryParams);
            }
        }

        Theme::breadcrumb()->add(__('Wishlist'), route('public.wishlist'));

        return Theme::scope('ecommerce.wishlist', compact('products'), 'plugins/ecommerce::themes.wishlist')->render();
    }

    public function store(int|string $productId, Request $request)
    {
        if (! EcommerceHelper::isWishlistEnabled()) {
            abort(404);
        }

        if (! $productId) {
            $productId = $request->input('product_id');
        }

        if (! $productId) {
            return $this->httpResponse()->setError()->setMessage(__('This product is not available.'));
        }

        $product = Product::query()->findOrFail($productId);

        $messageAdded = __('Added product :product successfully!', ['product' => $product->name]);
        $messageRemoved = __('Removed product :product from wishlist successfully!', ['product' => $product->name]);

        if (! auth('customer')->check()) {
            $wishlistInstance = Cart::instance('wishlist');

            $duplicates = $wishlistInstance->search(function ($cartItem) use ($productId) {
                return $cartItem->id == $productId;
            });

            if ($duplicates->isNotEmpty()) {
                $added = false;
                $duplicates->each(function ($cartItem, $rowId) use ($wishlistInstance) {
                    $wishlistInstance->remove($rowId);
                });
            } else {
                $added = true;
                $wishlistInstance
                    ->add($productId, $product->name, 1, $product->front_sale_price)
                    ->associate(Product::class);
            }

            return $this
                ->httpResponse()
                ->setMessage($added ? $messageAdded : $messageRemoved)
                ->setData([
                    'count' => Cart::instance('wishlist')->count(),
                    'added' => $added,
                ]);
        }

        $customer = auth('customer')->user();

        if (is_added_to_wishlist($productId)) {
            $added = false;
            Wishlist::query()->where([
                'product_id' => $productId,
                'customer_id' => $customer->getKey(),
            ])->delete();
        } else {
            $added = true;
            Wishlist::query()->create([
                'product_id' => $productId,
                'customer_id' => $customer->getKey(),
            ]);
        }

        return $this
            ->httpResponse()
            ->setMessage($added ? $messageAdded : $messageRemoved)
            ->setData([
                'count' => $customer->wishlist()->count(),
                'added' => $added,
            ]);
    }

    public function destroy(int|string $productId)
    {
        if (! EcommerceHelper::isWishlistEnabled()) {
            abort(404);
        }

        $product = Product::query()->findOrFail($productId);

        if (! auth('customer')->check()) {
            Cart::instance('wishlist')->search(function ($cartItem, $rowId) use ($productId) {
                if ($cartItem->id == $productId) {
                    Cart::instance('wishlist')->remove($rowId);

                    return true;
                }

                return false;
            });

            return $this
                ->httpResponse()
                ->setMessage(__('Removed product :product from wishlist successfully!', ['product' => $product->name]))
                ->setData(['count' => Cart::instance('wishlist')->count()]);
        }

        Wishlist::query()
            ->where([
                'product_id' => $productId,
                'customer_id' => auth('customer')->id(),
            ])
            ->delete();

        return $this
            ->httpResponse()
            ->setMessage(__('Removed product :product from wishlist successfully!', ['product' => $product->name]))
            ->setData(['count' => auth('customer')->user()->wishlist()->count()]);
    }
}
