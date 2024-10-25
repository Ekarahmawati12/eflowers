@extends('layouts.app')

@section('content')


<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Addresses</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="my-account.html" class="menu-link menu-link_us-s menu-link_active">Dashboard</a></li>
            <li><a href="{{route('user.orders')}}" class="menu-link menu-link_us-s">Orders</a></li>
            <li><a href="{{route('user.addresses')}}" class="menu-link menu-link_us-s">Addresses</a></li>
            <li>
                <form method="POST" action="{{route('logout')}}" id="logout-form">
                   @csrf
               <a href="{{route('logout')}}" class="menu-link menu-link_us-s" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
           </form>
           </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__address">
            <div class="row">
              <div class="col-6">
                <p class="notice">The following addresses will be used on the checkout page by default.</p>
              </div>
              
            </div>
            <div class="my-account__address-list row">
              <h5>Shipping Address</h5>

              @foreach ($addresses as $address)
              <div class="my-account__address-item col-md-6">
                  <div class="my-account__address-item__title">
                      <h5>{{ $address->name }} <i class="fa fa-check-circle text-success"></i></h5>
                  </div>
                  <div class="my-account__address-item__detail">
                      <p>{{ $address->address }}</p>
                      <p>{{ $address->locality }}, {{ $address->city }}, {{ $address->state }}</p>
                      <p>{{ $address->country }}, {{ $address->zip }}</p>
                      <p>Phone: {{ $address->phone }}</p>
                      <p>Type: {{ ucfirst($address->type) }}</p>
                      <p>Default: {{ $address->isdefault ? 'Yes' : 'No' }}</p>
                  </div>
              </div>
          @endforeach
            </div>
            <div class="wgp-pagination">
                {{ $addresses->links('pagination::bootstrap-5') }} <!-- Pagination -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
    
@endsection