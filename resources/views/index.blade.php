@extends('layouts.app')
@section('content')
<main>
<style>
.content {
    text-align: left; /* Pastikan teks diatur ke kiri */
    max-width: 600px; /* Atur lebar maksimum konten */
    margin: 0 auto; /* Pusatkan konten di halaman */
    padding: 20px; /* Tambahkan padding jika diperlukan */
}

h3 {
    margin-bottom: 10px; /* Ruang bawah untuk judul */
}

span {
    display: block; /* Pastikan span tampil sebagai blok agar berada di baris baru */
    margin-bottom: 10px; /* Ruang bawah untuk span */
    font-weight: bold; /* Atur ketebalan font jika perlu */
}

p.fw-bold {
    margin: 20px 0; /* Hapus auto untuk margin kiri dan kanan */
    padding: 10px; /* Ruang di dalam elemen */
}
.footer {
    padding: 20px 0; /* Atur padding footer */
}

.footer-column {
    margin-bottom: 10px; /* Jarak antar kolom */
}

.sub-menu__list {
    padding-left: 0;
    margin-top: 5px; /* Jarak atas */
    margin-bottom: 5px; /* Jarak bawah */
}

.product-item {
    margin-bottom: 10px; /* Jarak antar produk */
}
.category-carousel {
    margin-bottom: 0; /* Hapus margin bawah yang berlebihan */
    padding-bottom: 50px; /* Tambahkan padding bawah untuk membuat konten lebih panjang */
}
.money {
    font-family: 'Lora', serif;
    font-size: 18px;
    line-height: 24px;
    font-weight: bold; 
    color: #353333;
    margin-right: 5px; 
}


.price {
    font-family: 'Merriweather', serif; /* Ganti dengan font yang diinginkan */
    font-size: 16px; /* Ukuran font untuk harga */
    line-height: 20px; /* Tinggi garis untuk harga */
    font-weight: 700; /* Berat font untuk harga */
    color: #353333; /* Warna teks untuk harga */
}

 </style>   

    <section class="swiper-container js-swiper-slider swiper-number-pagination slideshow"
    data-settings='{
    "autoplay": {
      "delay": 5000
    },
    "slidesPerView": 1,
    "effect": "fade",
    "loop": true
  }'>
  
  <div class="swiper-wrapper">
    <!-- Slider untuk konten -->
    <div class="swiper-slide">
      <div class="overflow-hidden position-relative h-100">
        <div class="slideshow-character position-absolute bottom-0 pos_right-center">
         
        </div>
        <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
          <h6 class="text_dash text-uppercase fs-base fw-medium animate animate_fade animate_btt animate_delay-3">Eflowers</h6>
          <h2 class="h1 fw-normal mb-0 animate animate_fade animate_btt animate_delay-5">Fresh & Beautiful Blooms</h2>
          <p class="fw-bold animate animate_fade animate_btt animate_delay-5" style="line-height: 1.2;">
            At eflowers, we deliver the freshest flowers, perfect for any occasion.
         </p>
         <p class="fw-bold animate animate_fade animate_btt animate_delay-5" style="line-height: 1.2;">
            Handpicked to showcase nature’s beauty, our arrangements add elegance and charm to your day.
         </p>
         <p class="fw-bold animate animate_fade animate_btt animate_delay-5" style="line-height: 1.2;">
            Experience nature’s beauty, delivered right to your door.
         </p>
         
            <a href="{{route('shop.index')}}" class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop Now</a>
        </div>
      </div>
    </div>
</section>

    <div class="container mw-1620 bg-white border-radius-10">
        <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>
        <section class="category-carousel container">
            <h2 class="section-title text-center mb-3 pb-xl-2 mb-xl-4">You Might Like</h2>

            <div class="position-relative">
                <div class="swiper-container js-swiper-slider"
                    data-settings='{
          "autoplay": {
            "delay": 5000
          },
          "slidesPerView": 8,
          "slidesPerGroup": 1,
          "effect": "none",
          "loop": true,
          "navigation": {
            "nextEl": ".products-carousel__next-1",
            "prevEl": ".products-carousel__prev-1"
          },
          "breakpoints": {
            "320": {
              "slidesPerView": 2,
              "slidesPerGroup": 2,
              "spaceBetween": 15
            },
            "768": {
              "slidesPerView": 4,
              "slidesPerGroup": 4,
              "spaceBetween": 30
            },
            "992": {
              "slidesPerView": 6,
              "slidesPerGroup": 1,
              "spaceBetween": 45,
              "pagination": false
            },
            "1200": {
              "slidesPerView": 8,
              "slidesPerGroup": 1,
              "spaceBetween": 60,
              "pagination": false
            }
          }
        }'>
                    <div class="swiper-wrapper">
                        @foreach ($categories as $category)
                            
                        <div class="swiper-slide">
                            <img loading="lazy" class="w-100 h-auto mb-3" src="{{ asset('uploads/categories')}}/{{$category->image}}" width="124" height="124" alt="" />
                            <div class="text-center">
                                <a href="{{route('shop.index',['categories'=>$category->id])}}" class="menu-link fw-medium">{{$category->name}}</a>
                            </div>
                        </div>
                        @endforeach
                    </div><!-- /.swiper-wrapper -->
                </div><!-- /.swiper-container js-swiper-slider -->

                <div
                    class="products-carousel__prev products-carousel__prev-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_prev_md" />
                    </svg>
                </div><!-- /.products-carousel__prev -->
                <div
                    class="products-carousel__next products-carousel__next-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_next_md" />
                    </svg>
                </div><!-- /.products-carousel__next -->
            </div><!-- /.position-relative -->
        </section>

        <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

        <section class="products-grid container">
            <h2 class="section-title text-center mb-3 pb-xl-3 mb-xl-4">Featured Products</h2>

            <div class="row">
                @foreach ($fproducts as $fproduct)
                    
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card product-card_style3 mb-3 mb-md-4 mb-xxl-5">
                        <div class="pc__img-wrapper">
                            <a href="{{ route('shop.products.details', ['products_slug' => $fproduct->slug]) }}">
                                <img loading="lazy" src="{{ asset('uploads/products') }}/{{ $fproduct->image }}" width="330" height="400" alt="{{ $fproduct->name }}" class="pc__img">
                            </a>
                        </div>

                        <div class="pc__info position-relative">
                            <h6 class="pc__title"><a href="{{route('shop.products.details',['products_slug'=>$fproduct->slug])}}">{{$fproduct->name}}</a></h6>
                            <div class="product-card__price d-flex align-items-center">
                                <span class="money">Rp</span>
                                <span class="price">{{ number_format($fproduct->regular_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
     </div>
     <div class="text-center ">
        <a class="btn-link btn-link_lg default-underline text-uppercase fw-medium" href="#"></a>
      </div>
    </section>
    <div class="mb-0 pt-0 pb-0"></div>


  </div>


</section>
</div>
</main>
@endsection
