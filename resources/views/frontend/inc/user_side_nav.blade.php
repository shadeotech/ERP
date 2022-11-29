<div class="aiz-user-sidenav-wrap position-relative z-1 shadow-sm">
    <div class="aiz-user-sidenav rounded overflow-auto c-scrollbar-light pb-5 pb-xl-0">
        <div class="p-4 text-xl-center mb-4 border-bottom text-white position-relative" style="background:#1a1a27;">
            <span class="avatar avatar-md mb-3">
                @if (Auth::user()->avatar_original != null)
                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                @else
                    <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image rounded-circle" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                @endif
            </span>
            <h4 class="h5 fs-16 mb-1 fw-600">{{ Auth::user()->name }}</h4>
        </div>

        <div class="sidemnenu mb-3">
            <ul class="aiz-side-nav-list px-2" data-toggle="aiz-side-menu">

                <li class="aiz-side-nav-item">
                    <a href="{{ route('dashboard') }}" class="aiz-side-nav-link {{ areActiveRoutes(['dashboard'])}}">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Dashboard') }}</span>
                    </a>
                </li>

                @if(Auth::user()->user_type == 'delivery_boy')
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('assigned-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                            <i class="las la-hourglass-half aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Assigned Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('pickup-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                            <i class="las la-luggage-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Pickup Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('on-the-way-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                            <i class="las la-running aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('On The Way Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('completed-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                            <i class="las la-check-circle aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Completed Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('pending-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['pending-delivery'])}}">
                            <i class="las la-clock aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Pending Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('cancelled-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['cancelled-delivery'])}}">
                            <i class="las la-times-circle aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Cancelled Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('cancel-request-list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['cancel-request-list'])}}">
                            <i class="las la-times-circle aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Request Cancelled Delivery') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('total-collection') }}" class="aiz-side-nav-link {{ areActiveRoutes(['today-collection'])}}">
                            <i class="las la-comment-dollar aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Total Collections') }}
                            </span>
                        </a>
                    </li>
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('total-earnings') }}" class="aiz-side-nav-link {{ areActiveRoutes(['total-earnings'])}}">
                            <i class="las la-comment-dollar aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">
                                {{ translate('Total Earnings') }}
                            </span>
                        </a>
                    </li>
                @else
                
                    @php
                        $delivery_viewed = App\Order::where('user_id', Auth::user()->id)->where('delivery_viewed', 0)->get()->count();
                        $payment_status_viewed = App\Order::where('user_id', Auth::user()->id)->where('payment_status_viewed', 0)->get()->count();
                    @endphp
                    <!-- <li class="aiz-side-nav-item">
                        <a href="{{-- route('purchase_history.index') --}}" class="aiz-side-nav-link {{-- areActiveRoutes(['purchase_history.index']) --}}">
                            <i class="las la-file-alt aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{-- translate('Purchase History') --}}</span>
                            @if($delivery_viewed > 0 || $payment_status_viewed > 0)<span class="badge badge-inline badge-success">{{-- translate('New') --}}</span>@endif
                        </a>
                    </li> -->

                    <li class="aiz-side-nav-item">
                        <a href="{{ route('seller.file.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller.file.index'])}}">
                            <i class="las la-download aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Guides') }}</span>
                        </a
                    </li>

                    @php
                        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                        $club_point_addon = \App\Addon::where('unique_identifier', 'club_point')->first();
                    @endphp
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('customer_refund_request') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_refund_request'])}}">
                                <i class="las la-backward aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Sent Refund Request') }}</span>
                            </a>
                        </li>
                    @endif

                    <!--li class="aiz-side-nav-item">
                        <a href="{{ route('wishlists.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wishlists.index'])}}">
                            <i class="la la-heart-o aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Wishlist') }}</span>
                        </a>
                    </li-->

                    @if(Auth::user()->user_type == 'seller')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.products') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller.products', 'seller.products.upload', 'seller.products.edit'])}}">
                                <i class="las la-shopping-bag aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Products') }}</span>
                            </a>
                        </li>
                        <!--li class="aiz-side-nav-item">
                            <a href="{{route('product_bulk_upload.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['product_bulk_upload.index'])}}">
                                <i class="las la-upload aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Product Bulk Upload') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.digitalproducts') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller.digitalproducts', 'seller.digitalproducts.upload', 'seller.digitalproducts.edit'])}}">
                                <i class="lab la-sketch aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Parts') }}</span>
                            </a>
                        </li-->
                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('my_uploads.all') }}" class="aiz-side-nav-link {{ areActiveRoutes(['my_uploads.new'])}}">
                                <i class="las la-folder-open aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                            </a>
                        </li-->
                    @endif

                    <!--Fabric Start -->
                    @if(Auth::user()->user_type == 'seller')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('parts.list') }}" class="aiz-side-nav-link">
                                <i class="las la-cogs aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Parts</span>
                            </a>
                        </li>
                    @endif
                    <!--Fabric End -->

                    <!--Fabric Start -->
                    @if(Auth::user()->user_type == 'seller')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.fabric.list') }}" class="aiz-side-nav-link">
                                <i class="las la-chess-board aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Fabric Gallery</span>
                            </a>
                        </li>
                    @endif
                    <!--Fabric End -->

                    @if(get_setting('classified_product') == 1)
                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('customer_products.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_products.index', 'customer_products.create', 'customer_products.edit'])}}">
                                <i class="lab la-sketch aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Classified Products') }}</span>
                            </a>
                        </li-->
                    @endif

                    @if(Auth::user()->user_type == 'seller')
                        @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                            @if (\App\BusinessSetting::where('type', 'pos_activation_for_seller')->first() != null && get_setting('pos_activation_for_seller') != 0)
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('poin-of-sales.seller_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['poin-of-sales.seller_index'])}}">
                                        <i class="las la-fax aiz-side-nav-icon"></i>
                                        <span class="aiz-side-nav-text">{{ translate('POS Manager') }}</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        @php
                            $orders = DB::table('orders')
                                        ->orderBy('code', 'desc')
                                        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                                        ->where('order_details.seller_id', Auth::user()->id)
                                        ->where('orders.viewed', 0)
                                        ->select('orders.id')
                                        ->distinct()
                                        ->count();
                        @endphp
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('orders.main') }}" class="aiz-side-nav-link {{ areActiveRoutes(['orders.main'])}}">
                                <i class="las la-money-bill aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Orders') }}</span>
                                @if($orders > 0)<span class="badge badge-inline badge-success">{{ $orders }}</span>@endif
                            </a>
                        </li>

                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('vendor_refund_request') }}" class="aiz-side-nav-link {{ areActiveRoutes(['vendor_refund_request','reason_show'])}}">
                                    <i class="las la-backward aiz-side-nav-icon"></i>
                                    <span class="aiz-side-nav-text">{{ translate('Received Refund Request') }}</span>
                                </a>
                            </li>
                        @endif

                        @php
                            $review_count = DB::table('reviews')
                                        ->orderBy('code', 'desc')
                                        ->join('products', 'products.id', '=', 'reviews.product_id')
                                        ->where('products.user_id', Auth::user()->id)
                                        ->where('reviews.viewed', 0)
                                        ->select('reviews.id')
                                        ->distinct()
                                        ->count();
                        @endphp
                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('reviews.seller') }}" class="aiz-side-nav-link {{ areActiveRoutes(['reviews.seller'])}}">
                                <i class="las la-star-half-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Product Reviews') }}</span>
                                @if($review_count > 0)<span class="badge badge-inline badge-success">{{ $review_count }}</span>@endif
                            </a>
                        </li-->

                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('shops.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['shops.index'])}}">
                                <i class="las la-cog aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Shop Setting') }}</span>
                            </a>
                        </li-->

                        <!-- <li class="aiz-side-nav-item">
                            <a href="{{-- route('payments.index') --}}" class="aiz-side-nav-link {{-- areActiveRoutes(['payments.index']) --}}">
                                <i class="las la-history aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{-- translate('Payment History') --}}</span>
                            </a>
                        </li> -->

                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('withdraw_requests.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['withdraw_requests.index'])}}">
                                <i class="las la-money-bill-wave-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Money Withdraw') }}</span>
                            </a>
                        </li-->

                        <!--li class="aiz-side-nav-item">
                            <a href="{{ route('commission-log.index') }}" class="aiz-side-nav-link">
                                <i class="las la-file-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Commission History') }}</span>
                            </a>
                        </li-->

                    @endif

                    <!--@if (get_setting('conversation_system') == 1)
                        @php
                            $conversation = \App\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', 0)->get();
                        @endphp
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('conversations.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['conversations.index', 'conversations.show'])}}">
                                <i class="las la-comment aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Conversations') }}</span>
                                @if (count($conversation) > 0)
                                    <span class="badge badge-success">({{ count($conversation) }})</span>
                                @endif
                            </a>
                        </li>
                    @endif


                    @if (get_setting('wallet_system') == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('wallet.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wallet.index'])}}">
                                <i class="las la-dollar-sign aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('My Wallet')}}</span>
                            </a>
                        </li>
                    @endif-->

                    @if ($club_point_addon != null && $club_point_addon->activated == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('earnng_point_for_user') }}" class="aiz-side-nav-link {{ areActiveRoutes(['earnng_point_for_user'])}}">
                                <i class="las la-dollar-sign aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Earning Points')}}</span>
                            </a>
                        </li>
                    @endif

                    @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                        <li class="aiz-side-nav-item">
                            <a href="javascript:void(0);" class="aiz-side-nav-link {{ areActiveRoutes(['affiliate.user.index', 'affiliate.payment_settings'])}}">
                                <i class="las la-dollar-sign aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Affiliate') }}</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('affiliate.user.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Affiliate System') }}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('affiliate.user.payment_history') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Payment History') }}</span>
                                    </a>
                                </li>
                                <!--li class="aiz-side-nav-item">
                                    <a href="{{ route('affiliate.user.withdraw_request_history') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Withdraw request history') }}</span>
                                    </a>
                                </li-->
                            </ul>
                        </li>
                    @endif

                    @php
                        $support_ticket = DB::table('tickets')
                                    ->where('client_viewed', 0)
                                    ->where('user_id', Auth::user()->id)
                                    ->count();
                    @endphp

                    <li class="aiz-side-nav-item">
                        <a href="{{ route('support_ticket.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['support_ticket.index'])}}">
                            <i class="las la-file-invoice-dollar aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Claim/Support Ticket')}}</span>
                            @if($support_ticket > 0)<span class="badge badge-inline badge-success">{{ $support_ticket }}</span> @endif
                        </a>
                    </li>
                @endif

                <li class="aiz-side-nav-item">
                    <a href="{{route('cart.seller.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['cart.seller.index'])}}">
                        <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Cart</span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{route('saved_carts.seller.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['saved_carts.seller.index'])}}">
                        <i class="las la-cart-plus aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Saved Quotes</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('profile') }}" class="aiz-side-nav-link {{ areActiveRoutes(['profile'])}}">
                        <i class="las la-user aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Manage Profile')}}</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('logout') }}" class="aiz-side-nav-link {{ areActiveRoutes(['logout'])}}">
                        <i class="las la-sign-out-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Logout')}}</span>
                    </a>
                </li>

            </ul>
        </div>
        @if (get_setting('vendor_system_activation') == 1 && Auth::user()->user_type == 'customer')
            <div>
                <a href="{{ route('shops.create') }}" class="btn btn-block btn-soft-primary rounded-0">
                    </i>{{ translate('Be A Dealer') }}
                </a>
            </div>
        @endif
        @if(Auth::user()->user_type == 'seller')
          <hr>
          <h4 class="h5 fw-600 text-center">Spent this month</h4>
          @php
              $date = date("Y-m-d");
              $days_ago_30 = date('Y-m-d', strtotime('-30 days', strtotime($date)));
              $days_ago_60 = date('Y-m-d', strtotime('-60 days', strtotime($date)));
          @endphp
          <div class="widget-balance pb-3 pt-1" style="min-height:100vh;">
            <div class="text-center">
                <div  class="heading-4 strong-700 mb-4">
                    @php
                        $orderTotal = \App\Models\Order::where('user_id', Auth::user()->id)->where('created_at', '>=', $days_ago_30)->sum('grand_total');
                    @endphp
                    <small class="d-block fs-12 mb-2">{{ translate('Your spent amount (current month)')}}</small>
                    <a href="/orders/main" class="btn btn-primary fw-600 fs-18">{{ single_price($orderTotal) }}</a>
                </div>
                <hr>
                <table class="table table-borderless">
                   
                    <tr>
                        @php
                            $orderTotal = \App\Order::where('seller_id', Auth::user()->id)->where("payment_status", 'paid')->where('created_at', '>=', $days_ago_60)->where('created_at', '<=', $days_ago_30)->sum('grand_total');
                        @endphp
                        <!-- <td class="p-1" width="60%">
                            {{ translate('Last Month Sold')}}:
                        </td> -->
                        <!-- <td class="p-1 fw-600" width="40%">
                            {{ single_price($orderTotal) }}
                        </td> -->
                    </tr>
                </table>
            </div>
        </div>
        @endif
        
    </div>

    <div class="fixed-bottom d-xl-none bg-white border-top d-flex justify-content-between px-2" style="box-shadow: 0 -5px 10px rgb(0 0 0 / 10%);">
        <a class="btn btn-sm p-2 d-flex align-items-center" href="{{ route('logout') }}">
            <i class="las la-sign-out-alt fs-18 mr-2"></i>
            <span>{{ translate('Logout') }}</span>
        </a>
        <button class="btn btn-sm p-2 " data-toggle="class-toggle" data-backdrop="static" data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb">
            <i class="las la-times la-2x"></i>
        </button>
    </div>
</div>
