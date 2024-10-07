@extends('layouts.app')
@section('content')

<style>
    .field-heart{
        color:red;
    }
</style>

<main class="pt-90">
    <section class="shop-main container pt-4 pt-xl-5">  
        
        <!-- Form Filter untuk Category dan Price -->
        <form action="{{ route('shop.index') }}" method="GET" class="filter-form mb-5 w-100">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        

        <!-- Grid Produk -->
        <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
            @foreach ($products as $product)
            <div class="product-card-wrapper">
                <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                    <div class="pc__img-wrapper">
                        <div class="swiper-container background-img js-swiper-slider" data-settings='{"resizeObserver": true}'>
                          <div class="swiper-wrapper">
                            <div class="swiper-slide">
                              <a href="{{route('shop.products.details',['products_slug'=>$product->slug])}}"><img loading="lazy" src="{{asset('uploads/products')}}/{{$product->image}}" width="330"
                                  height="400" alt="{{$product->name}}" class="pc__img"></a>
                            </div>
                          </div>
                          <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                              xmlns="http://www.w3.org/2000/svg">
                              <use href="#icon_prev_sm" />
                            </svg></span>
                          <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                              xmlns="http://www.w3.org/2000/svg">
                              <use href="#icon_next_sm" />
                            </svg></span>
                        </div>
                        @if(Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                        <a href="{{route('cart.index')}}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to cart</a>
                    @else
                    <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}" />
                        <input type="hidden" name="quantity" value="1" />
                        <input type="hidden" name="name" value="{{ $product->name }}" />
                        <input type="hidden" name="price" value="{{ $product->regular_price != '' ? $product->regular_price : 0 }}" />  
                        <button type="submit" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium" 
                        data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                    </form>
                     @endif
                      </div>
                    
                    <div class="pc__info position-relative">
                        <p class="pc__category">{{ $product->category->name }}</p>
                        <h6 class="pc__title">
                            <a href="{{route('shop.products.details',['products_slug'=>$product->slug])}}">
                                {{ $product->name }}
                            </a>
                        </h6>
                        <div class="product-card__price d-flex">
                            <span class="money price">${{ $product->regular_price }}</span>
                        </div>
                        <div class="product-card__review d-flex align-items-center">
                            <div class="reviews-group d-flex">
                                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_star" />
                                </svg>
                                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_star" />
                                </svg>
                                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_star" />
                                </svg>
                                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_star" />
                                </svg>
                                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_star" />
                                </svg>
                            </div>
                            <span class="reviews-note text-lowercase text-secondary ms-1">8k+ reviews</span>
                        </div>
                        @if(Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                        <form method="POST" action="{{route('wishlist.item.remove',['rowId'=>Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId])}}">
                            @csrf
                            @method('DELETE')
                        <button  type ="submit"class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist field-heart" 
                        title="Remove from Wishlist">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_heart" />
                            </svg>
                        </button>
                        </form>
                        @else
                        <form method="POST" action="{{route('wishlist.add')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$product->id}}" />
                            <input type="hidden" name="name" value="{{$product->name}}" />
                            <input type="hidden" name="price" value="{{ $product->regular_price != '' ? $product->regular_price : 0 }}" />  
                            <input type="hidden" name="quantity" value="1" />
                        <button  type ="submit"class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist" 
                        title="Add To Wishlist">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_heart" />
                            </svg>
                        </button>
                    </form>
                    @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <div class="divider"></div>
        <div class="flex items-center justify-between flex-warp-gap10 wpg-pagination">
            {{$products->links('pagination::bootstrap-5')}}
        </div>

    </section>
</main>    
@endsection