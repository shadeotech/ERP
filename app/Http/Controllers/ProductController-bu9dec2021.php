<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductTranslation;
use App\ProductStock;
use App\Category;
use App\FlashDealProduct;
use App\ProductTax;
use App\Attribute;
use App\AttributeValue;
use App\Cart;
use App\Language;
use Auth;
use App\SubSubCategory;
use Session;
use Carbon\Carbon;
use ImageOptimizer;
use DB;
use Combinations;
use CoreComponentRepository;
use Illuminate\Support\Str;
use Artisan;
use App\Models\XztFabric;
use App\Models\ProductFabric;
use App\Models\XztCassette;
use App\Models\ProductCassette;
use App\Models\XztMount;
use App\Models\ProductMount;
use App\Models\XztBracket;
use App\Models\ProductBracket;
use App\Models\XztSpringassist;
use App\Models\ProductSpringassist;
use App\Models\XztRoomtype;
use App\Models\ProductRoomtype;
use App\Models\XztStack;
use App\Models\ProductStack;
use App\Models\XztManualCts;
use App\Models\XztMotorCts;
use App\Models\XztWidMotors;
use App\Models\XztWand;
use App\Models\ProductControltype;
use App\Models\XztWrap;
use App\Models\ProductWrap;
use App\Models\ProductWand;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        //CoreComponentRepository::instantiateShopRepository();

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        // $products = Product::where('added_by', 'admin');
        $products = Product::where('added_by', 'admin')->where('is_parts', 0);

        if ($request->type != null){
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null){
            $products = $products
                        ->where('name', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller');
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null){
            $products = $products
                        ->where('name', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }
        if ($request->searchcat != null){
            $products = $products
                        ->where('name', 'like', '%'.$request->searchcat.'%')
                        // ->orwhere('description', 'like', '%'.$request->searchcat.'%')
                        ;
            $sort_search = $request->searchcat;
        }

        if ($request->type != null){
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';
        
        return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    public function all_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::orderBy('created_at', 'desc');
        
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null){
            $products = $products
                        ->where('name', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }
        if ($request->type != null){
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->paginate(15);
        // dd($products);
        $type = 'All';

        return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        $fabrics = XztFabric::all();
        $cassettes = XztCassette::select('cassette_code', 'name', 'category_id')->distinct()->get();
        $mounts = XztMount::all();
        $brackets = XztBracket::all();
        $springassists = XztSpringassist::all();
        $roomtypes = XztRoomtype::all();
        $stacks = XztStack::all();
        $wraps = XztWrap::select('wrap_code', 'name')->distinct()->get();
        $ct_wid_motors = XztWidMotors::select('ct_code', 'name')->distinct()->get();
        $ct_motors = XztMotorCts::all();
        $ct_manuals = XztManualCts::all();
        $wands = XztWand::all();
        // $test_first = XztWidMotors::where('ct_code', 'willow_motor_01')->min('min_wid');
        // $test_last = XztWidMotors::where('ct_code', 'willow_motor_01')->max('max_wid');
        return view('backend.product.products.create', compact('categories', 'fabrics', 'cassettes', 'mounts', 'brackets', 'springassists', 'roomtypes', 'stacks', 'wraps', 'ct_wid_motors', 'ct_motors', 'ct_manuals', 'wands' ));
    }

    public function add_more_choice_option(Request $request) {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
        //  $val = $row->id . ' | ' . $row->name;
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }


        echo json_encode($html);
        // $html = '';

        // $html .= '<div class="form-group row">
        //             <div class="col-md-3">
        //                 <input type="hidden" name="choice_no[]" value="'. $request->id .'">
        //                 <input type="text" class="form-control" name="choice[]" value="'.$all_attribute_values->attribute->name.'" placeholder="'.translate('Choice Title').'" readonly>
        //             </div>
        //             <div class="col-md-8">
        //                 <input type="text" class="form-control aiz-tag-input" name="choice_options_'. $request->id .'[]" placeholder="'. translate('Enter choice values') .'" data-on-change="update_sku">
        //                 <select class="form-control aiz-selectpicker" data-live-search="true" name="choice_options_'. $request->id .'[]" multiple>
        //                     <option value="">'. translate('Enter choice values') .'</option>
        //                 </select>
        //             </div>
        //         </div>';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->wrap);
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();

        $product = new Product;
        $product->name = $request->name;
        $product->added_by = $request->added_by;
        if(Auth::user()->user_type == 'seller'){
            $product->user_id = Auth::user()->id;
            if(get_setting('product_approve_by_admin') == 1) {
                $product->approved = 0;
            }
        }
        else{
            $product->user_id = \App\User::where('user_type', 'admin')->first()->id;
        }
        $product->category_id = $request->category_id;
        // $product->brand_id = $request->brand_id;
        // $product->barcode = $request->barcode;

        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            }
            else {
                $product->refundable = 0;
            }
        }
        // $product->photos = $request->photos;
        //$product->thumbnail_img = $request->thumbnail_img;
        // $product->unit = $request->unit;
        // $product->min_qty = $request->min_qty;
        // $product->low_stock_quantity = $request->low_stock_quantity;
        // $product->stock_visibility_state = $request->stock_visibility_state;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags = implode(',', $tags);

        $product->description = $request->description;
        
        if($request->hasFile('thumbnail_img')) {
            $file = $request->file('thumbnail_img');
            $name = $file->getClientOriginalName();
            $name = time().'-'.$name;
            $file->move(public_path().'/products/images', $name); 
            $product->thumbnail_img = $name;
        }
        // $product->video_provider = $request->video_provider;
        // $product->video_link = $request->video_link;
        // $product->unit_price = $request->unit_price;
        // $product->discount = $request->discount;
        // $product->discount_type = $request->discount_type;

        // if ($request->date_range != null) {
        //     $date_var               = explode(" to ", $request->date_range);
        //     $product->discount_start_date = strtotime($date_var[0]);
        //     $product->discount_end_date   = strtotime( $date_var[1]);
        // }

        // $product->shipping_type = $request->shipping_type;
        // $product->est_shipping_days  = $request->est_shipping_days;

        // if (\App\Addon::where('unique_identifier', 'club_point')->first() != null &&
        //         \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
        //     if($request->earn_point) {
        //         $product->earn_point = $request->earn_point;
        //     }
        // }

        // if ($request->has('shipping_type')) {
        //     if($request->shipping_type == 'free'){
        //         $product->shipping_cost = 0;
        //     }
        //     elseif ($request->shipping_type == 'flat_rate') {
        //         $product->shipping_cost = $request->flat_shipping_cost;
        //     }
        //     elseif ($request->shipping_type == 'product_wise') {
        //         $product->shipping_cost = json_encode($request->shipping_cost);
        //     }
        // }
        // if ($request->has('is_quantity_multiplied')) {
        //     $product->is_quantity_multiplied = 1;
        // }

        // $product->meta_title = $request->meta_title;
        // $product->meta_description = $request->meta_description;

        // if($request->has('meta_img')){
        //     $product->meta_img = $request->meta_img;
        // } else {
        //     $product->meta_img = $product->thumbnail_img;
        // }

        if($product->meta_title == null) {
            $product->meta_title = $product->name;
        }

        if($product->meta_description == null) {
            $product->meta_description = strip_tags($product->description);
        }

        // if($product->meta_img == null) {
        //     $product->meta_img = $product->thumbnail_img;
        // }

        if($request->hasFile('pdf')){
            $product->pdf = $request->pdf->store('uploads/products/pdf');
        }

        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);

        // if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
        //     $product->colors = json_encode($request->colors);
        // }
        // else {
        //     $colors = array();
        //     $product->colors = json_encode($colors);
        // }

        // $choice_options = array();

        // if($request->has('choice_no')){
        //     foreach ($request->choice_no as $key => $no) {
        //         $str = 'choice_options_'.$no;

        //         $item['attribute_id'] = $no;

        //         $data = array();
        //         // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
        //         foreach ($request[$str] as $key => $eachValue) {
        //             // array_push($data, $eachValue->value);
        //             array_push($data, $eachValue);
        //         }

        //         $item['values'] = $data;
        //         array_push($choice_options, $item);
        //     }
        // }

        // if (!empty($request->choice_no)) {
        //     $product->attributes = json_encode($request->choice_no);
        // }
        // else {
        //     $product->attributes = json_encode(array());
        // }

        // $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);

        $product->published = 1;
        if($request->button == 'unpublish' || $request->button == 'draft') {
            $product->published = 0;
        }

        // if ($request->has('cash_on_delivery')) {
        //     $product->cash_on_delivery = 1;
        // }
        // if ($request->has('featured')) {
        //     $product->featured = 1;
        // }
        // if ($request->has('todays_deal')) {
        //     $product->todays_deal = 1;
        // }
        // $product->cash_on_delivery = 0;
        // if ($request->cash_on_delivery) {
        //     $product->cash_on_delivery = 1;
        // }
        //$variations = array();
        if($request->has('state')) {$product->state = $request->state;}
        if($request->has('shade_code')) {$product->shade_code = $request->shade_code;}

        $product->save();

        //Product Fabric
        if($request->has('fabric')) {
            foreach($request->fabric as $item) {
                $prd_fab = new ProductFabric();
                $prd_fab->product_id = $product->id;
                $prd_fab->fabric_id = $item;
                $prd_fab->save();
            }
        }

        //Cassette
        if($request->has('cassette')) {
            foreach($request->cassette as $item) {
                // dd($item);
                $prd_cassette = new ProductCassette();
                $prd_cassette->product_id = $product->id;
                $prd_cassette->cassette_code = $item;
                $prd_cassette->save();
            }
        }

        //Mount
        if($request->has('mount')) {
            foreach($request->mount as $item) {
                $prd_mount = new ProductMount();
                $prd_mount->product_id = $product->id;
                $prd_mount->mount_id = $item;
                $prd_mount->save();
            }
        }
        
        //Bracket
        if($request->has('bracket')) {
            foreach($request->bracket as $item) {
                $prd_bracket = new ProductBracket();
                $prd_bracket->product_id = $product->id;
                $prd_bracket->bracket_id = $item;
                $prd_bracket->save();
            }
        }
        
        //Springassist
        if($request->has('springassist')) {
            foreach($request->springassist as $item) {
                $prd_springassist = new ProductSpringassist();
                $prd_springassist->product_id = $product->id;
                $prd_springassist->springassist_id = $item;
                $prd_springassist->save();
            }
        }
        
        //Room Types
        if($request->has('roomtype')) {
            foreach($request->roomtype as $item) {
                $prd_roomtype = new ProductRoomtype();
                $prd_roomtype->product_id = $product->id;
                $prd_roomtype->roomtype_id = $item;
                $prd_roomtype->save();
            }
        }
        
        //Stacks
        if($request->has('stack')) {
            foreach($request->stack as $item) {
                $prd_stack = new ProductStack();
                $prd_stack->product_id = $product->id;
                $prd_stack->stack_id = $item;
                $prd_stack->save();
            }
        }
        
        //Fabric Wrapped
        if($request->has('wrap')) {
            if(!empty($request->wrap)) {
                $prd_wrap = new ProductWrap();
                $prd_wrap->product_id = $product->id;
                $prd_wrap->wrap_code = $request->wrap;
                $prd_wrap->save();
            }
        }
        
        //CT Manual
        if($request->has('ct_manual')) {
            foreach($request->ct_manual as $item) {
                $prd_ct_manual = new ProductControltype();
                $prd_ct_manual->product_id = $product->id;
                $prd_ct_manual->ct_manual_id = $item;
                $prd_ct_manual->save();
            }
        }

        //CT Motor
        if($request->has('ct_motor')) {
            foreach($request->ct_motor as $item) {
                $prd_ct_motor = new ProductControltype();
                $prd_ct_motor->product_id = $product->id;
                $prd_ct_motor->ct_motor_id = $item;
                $prd_ct_motor->save();
            }
        }
        
        //CT Width Motor
        if($request->has('ct_wid_motor')) {
            if(!empty($request->ct_wid_motor)) {
                $prd_ct_wid_motor = new ProductControltype();
                $prd_ct_wid_motor->product_id = $product->id;
                $prd_ct_wid_motor->ct_widmotor_code = $request->ct_wid_motor;
                $prd_ct_wid_motor->save();
            }
        }

        //Wand
        if($request->has('wand')) {
            foreach($request->wand as $item) {
                $prd_wand = new ProductWand();
                $prd_wand->product_id = $product->id;
                $prd_wand->wand_id = $item;
                $prd_wand->save();
            }
        }
        
        //Specification
        if($request->has('specification')) {
            $product->specification = $request->specification;
        }

        //VAT & Tax
        /*
        if($request->tax_id) {
            foreach ($request->tax_id as $key => $val) {
                $product_tax = new ProductTax;
                $product_tax->tax_id = $val;
                $product_tax->product_id = $product->id;
                $product_tax->tax = $request->tax[$key];
                $product_tax->tax_type = $request->tax_type[$key];
                $product_tax->save();
            }
        }
        //Flash Deal
        if($request->flash_deal_id) {
            $flash_deal_product = new FlashDealProduct;
            $flash_deal_product->flash_deal_id = $request->flash_deal_id;
            $flash_deal_product->product_id = $product->id;
            $flash_deal_product->save();
        }

        //combinations start
        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = array();
                foreach ($request[$name] as $key => $eachValue) {
                    array_push($data, $eachValue);
                }
                array_push($options, $data);
            }
        }

        //Generates the combinations of customer choice options
        $combinations = Combinations::makeCombinations($options);
        if(count($combinations[0]) > 0){
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination){
                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                    }
                    else{
                        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        }
                        else{
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
                if($product_stock == null){
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                }

                $product_stock->variant = $str;
                $product_stock->price = $request['price_'.str_replace('.', '_', $str)];
                $product_stock->sku = $request['sku_'.str_replace('.', '_', $str)];
                $product_stock->qty = $request['qty_'.str_replace('.', '_', $str)];
                $product_stock->image = $request['img_'.str_replace('.', '_', $str)];
                $product_stock->save();
            }
        }
        else{
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product->id;
            $product_stock->variant     = '';
            $product_stock->price       = $request->unit_price;
            $product_stock->sku         = $request->sku;
            $product_stock->qty         = $request->current_stock;
            $product_stock->save();
        }
        //combinations end
        */

	    $product->save();

        // Product Translations
        $product_translation = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_id' => $product->id]);
        $product_translation->name = $request->name;
        $product_translation->unit = $request->unit;
        $product_translation->description = $request->description;
        $product_translation->save();

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
            return redirect()->route('products.admin');
        }
        else{
            if(\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }
            return redirect()->route('seller.products');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function admin_product_edit(Request $request, $id)
     {
        $product = Product::findOrFail($id);
        if($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }

        // $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        
        $fabrics = XztFabric::all();
        $cassettes = XztCassette::select('cassette_code', 'name', 'category_id')->distinct()->get();
        // $cassettes = XztCassette::select('cassette_code', 'name')->distinct()->get();
        $mounts = XztMount::all();
        $brackets = XztBracket::all();
        $springassists = XztSpringassist::all();
        $roomtypes = XztRoomtype::all();
        $stacks = XztStack::all();
        $ct_wid_motors = XztWidMotors::select('ct_code', 'name')->distinct()->get();
        $ct_motors = XztMotorCts::all();
        $ct_manuals = XztManualCts::all();
        $wraps = XztWrap::select('wrap_code', 'name')->distinct()->get();
        
        $fabric_selected = ProductFabric::select('fabric_id')->where('product_id', $id)->get();
        $fabric_array = [];
        foreach($fabric_selected as $item) {
            $fabric_array[] = $item->fabric_id;
        }

        $cass_selected = ProductCassette::select('cassette_code')->where('product_id', $id)->get();
        $cass_array = [];
        foreach($cass_selected as $item) {
            $cass_array[] = $item->cassette_code;
        }

        $mount_selected = ProductMount::select('mount_id')->where('product_id', $id)->get();
        $mount_array = [];
        foreach($mount_selected as $item) {
            $mount_array[] = $item->mount_id;
        }

        $bracket_selected = ProductBracket::select('bracket_id')->where('product_id', $id)->get();
        $bracket_array = [];
        foreach($bracket_selected as $item) {
            $bracket_array[] = $item->bracket_id;
        }

        $sa_selected = ProductSpringassist::select('springassist_id')->where('product_id', $id)->get();
        $sa_array = [];
        foreach($sa_selected as $item) {
            $sa_array[] = $item->springassist_id;
        }

        $roomtype_selected = ProductRoomtype::select('roomtype_id')->where('product_id', $id)->get();
        $roomtype_array = [];
        foreach($roomtype_selected as $item) {
            $roomtype_array[] = $item->roomtype_id;
        }

        $stack_selected = ProductStack::select('stack_id')->where('product_id', $id)->get();
        $stack_array = [];
        foreach($stack_selected as $item) {
            $stack_array[] = $item->stack_id;
        }

        $wrap_selected = ProductWrap::select('wrap_code')->where('product_id', $id)->first();
        if(empty($wrap_selected)) {
            $wrap_selected = (object) ['wrap_code' => ""];
        }

        $ct_man_selected = ProductControltype::select('ct_manual_id')->where('product_id', $id)->where('ct_manual_id', '<>', null)->get();
        $ct_motor_selected = ProductControltype::select('ct_motor_id')->where('product_id', $id)->where('ct_motor_id', '<>', null)->get();
        $ct_wm_selected = ProductControltype::select('ct_widmotor_code')->where('product_id', $id)->where('ct_widmotor_code', '<>', null)->first();
        $ct_man_array = [];
        foreach($ct_man_selected as $item) {
            $ct_man_array[] = $item->ct_manual_id;
        }
        $ct_motor_array = [];
        foreach($ct_motor_selected as $item) {
            $ct_motor_array[] = $item->ct_motor_id;
        }
        if(empty($ct_wm_selected)) {
            $ct_wm_selected = (object) ['ct_widmotor_code' => ""];
        }
        // $ct_wm_array = [];
        // foreach($ct_wm_selected as $item) {
        //     $ct_wm_array[] = $item->ct_widmotor_code;
        // }
        // dd($bracket_array);

        return view('backend.product.products.edit_product', compact('product', 'categories', 'tags', 'fabrics', 'fabric_array', 'cassettes', 'cass_array', 'mounts', 'mount_array', 'brackets', 'bracket_array', 'springassists', 'sa_array', 'roomtypes', 'roomtype_array', 'stacks', 'stack_array', 'wraps', 'wrap_selected', 'ct_manuals',  'ct_motors',  'ct_wid_motors',  'ct_man_array',  'ct_motor_array',  'ct_wm_selected', ));
        // return view('backend.product.products.edit', compact('product', 'categories', 'tags','lang'));
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::all();
        return view('backend.product.products.edit', compact('product', 'categories', 'tags','lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function admin_product_update(Request $request, $id) {

        // dd($request->input());
        $rec = Product::find($id);
        if($request->has('name')) {$rec->name = $request->name;}
        if($request->has('description')) {$rec->description = $request->description;}
        if($request->has('specification')) {$rec->specification = $request->specification;}
        if($request->has('category_id')) {$rec->category_id = $request->category_id;}
        // if($request->has('shade_code')) {$rec->shade_code = $request->shade_code;}
        $tags = array();
        if($request->tags[0] != null && $request->has('tags')){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
            $rec->tags = implode(',', $tags);
        }
        if($request->has('state')) {
            $rec->state = $request->state;
        }else {
            $rec->state = "Inactive";
        }
        if($request->hasFile('thumbnail_img')) {
            if(file_exists(public_path('/products/images'.'/'.$rec->thumbnail_img))) {
                unlink(public_path('/products/images'.'/'.$rec->thumbnail_img));
            }
            $file = $request->file('thumbnail_img');
            $name = $file->getClientOriginalName();
            $name = time().'-'.$name;
            $file->move(public_path().'/products/images', $name); 
            $rec->thumbnail_img = $name;
        }

        $rec->save();
        
        //Fabric
        if($request->has('fabric')) {
            $del = ProductFabric::where('product_id', $id)->delete();
            foreach($request->fabric as $item) {
                $prd_fab = new ProductFabric();
                $prd_fab->product_id = $rec->id;
                $prd_fab->fabric_id = $item;
                $prd_fab->save();
            }
        }

        //Cassette
        if($request->has('cassette')) {
            $del = ProductCassette::where('product_id', $id)->delete();
            foreach($request->cassette as $item) {
                $prd_cassette = new ProductCassette();
                $prd_cassette->product_id = $rec->id;
                $prd_cassette->cassette_code = $item;
                $prd_cassette->save();
            }
        }

        //Mount
        if($request->has('mount')) {
            $del = ProductMount::where('product_id', $id)->delete();
            foreach($request->mount as $item) {
                $prd_mount = new ProductMount();
                $prd_mount->product_id = $rec->id;
                $prd_mount->mount_id = $item;
                $prd_mount->save();
            }
        }
        
        //Bracket
        if($request->has('bracket')) {
            $del = ProductBracket::where('product_id', $id)->delete();
            foreach($request->bracket as $item) {
                $prd_bracket = new ProductBracket();
                $prd_bracket->product_id = $rec->id;
                $prd_bracket->bracket_id = $item;
                $prd_bracket->save();
            }
        }
        
        //Springassist
        if($request->has('springassist')) {
            $del = ProductSpringassist::where('product_id', $id)->delete();
            foreach($request->springassist as $item) {
                $prd_springassist = new ProductSpringassist();
                $prd_springassist->product_id = $rec->id;
                $prd_springassist->springassist_id = $item;
                $prd_springassist->save();
            }
        }
        
        //Room Types
        if($request->has('roomtype')) {
            $del = ProductRoomtype::where('product_id', $id)->delete();
            foreach($request->roomtype as $item) {
                $prd_roomtype = new ProductRoomtype();
                $prd_roomtype->product_id = $rec->id;
                $prd_roomtype->roomtype_id = $item;
                $prd_roomtype->save();
            }
        }
        
        //Stacks
        if($request->has('stack')) {
            $del = ProductStack::where('product_id', $id)->delete();
            foreach($request->stack as $item) {
                $prd_stack = new ProductStack();
                $prd_stack->product_id = $rec->id;
                $prd_stack->stack_id = $item;
                $prd_stack->save();
            }
        }
        
        //Fabric Wrapped
        if($request->has('wrap')) {
            $del = ProductWrap::where('product_id', $id)->delete();
            if(!empty($request->wrap)) {
                $prd_wrap = new ProductWrap();
                $prd_wrap->product_id = $rec->id;
                $prd_wrap->wrap_code = $request->wrap;
                $prd_wrap->save();
            }
        }
        
        //CT Manual
        if($request->has('ct_manual')) {
            $del = ProductControltype::where('product_id', $id)->where('ct_manual_id', '<>', null)->delete();
            foreach($request->ct_manual as $item) {
                $prd_ct_manual = new ProductControltype();
                $prd_ct_manual->product_id = $rec->id;
                $prd_ct_manual->ct_manual_id = $item;
                $prd_ct_manual->save();
            }
        }

        // //CT Motor
        if($request->has('ct_motor')) {
            $del = ProductControltype::where('product_id', $id)->where('ct_motor_id', '<>', null)->delete();
            foreach($request->ct_motor as $item) {
                $prd_ct_motor = new ProductControltype();
                $prd_ct_motor->product_id = $rec->id;
                $prd_ct_motor->ct_motor_id = $item;
                $prd_ct_motor->save();
            }
        }
        
        // //CT Width Motor
        if($request->has('ct_wid_motor')) {
            $del = ProductControltype::where('product_id', $id)->where('ct_widmotor_code', '<>', null)->delete();
            if(!empty($request->ct_wid_motor)) {
                $prd_ct_wid_motor = new ProductControltype();
                $prd_ct_wid_motor->product_id = $rec->id;
                $prd_ct_wid_motor->ct_widmotor_code = $request->ct_wid_motor;
                $prd_ct_wid_motor->save();
            }
        }
        return redirect()->route('products.admin');


    }
    
    
     public function update(Request $request, $id)
    {
        // dd($request->input());
        $refund_request_addon       = \App\Addon::where('unique_identifier', 'refund_request')->first();
        $product                    = Product::findOrFail($id);
        $product->category_id       = $request->category_id;
        $product->brand_id          = $request->brand_id;
        $product->barcode           = $request->barcode;
        $product->cash_on_delivery = 0;
        $product->featured = 0;
        $product->todays_deal = 0;
        $product->is_quantity_multiplied = 0;


        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            }
            else {
                $product->refundable = 0;
            }
        }

        if($request->lang == env("DEFAULT_LANGUAGE")){
            $product->name          = $request->name;
            $product->unit          = $request->unit;
            $product->description   = $request->description;
            $product->slug          = strtolower($request->slug);
        }

        $product->photos                 = $request->photos;
        //$product->thumbnail_img          = $request->thumbnail_img;
        $product->min_qty                = $request->min_qty;
        $product->low_stock_quantity     = $request->low_stock_quantity;
        $product->stock_visibility_state = $request->stock_visibility_state;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags           = implode(',', $tags);

        $product->video_provider = $request->video_provider;
        $product->video_link     = $request->video_link;
        $product->unit_price     = $request->unit_price;
        $product->discount       = $request->discount;
        $product->discount_type     = $request->discount_type;

        if ($request->date_range != null) {
            $date_var               = explode(" to ", $request->date_range);
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date   = strtotime( $date_var[1]);
        }

        $product->shipping_type  = $request->shipping_type;
        $product->est_shipping_days  = $request->est_shipping_days;

        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null &&
                \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if($request->earn_point) {
                $product->earn_point = $request->earn_point;
            }
        }

        if ($request->has('shipping_type')) {
            if($request->shipping_type == 'free'){
                $product->shipping_cost = 0;
            }
            elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
            elseif ($request->shipping_type == 'product_wise') {
                $product->shipping_cost = json_encode($request->shipping_cost);
            }
        }

        if ($request->has('is_quantity_multiplied')) {
            $product->is_quantity_multiplied = 1;
        }
        if ($request->has('cash_on_delivery')) {
            $product->cash_on_delivery = 1;
        }

        if ($request->has('featured')) {
            $product->featured = 1;
        }

        if ($request->has('todays_deal')) {
            $product->todays_deal = 1;
        }

        // $product->meta_title        = $request->meta_title;
        // $product->meta_description  = $request->meta_description;
        // $product->meta_img          = $request->meta_img;

        if($product->meta_title == null) {
            $product->meta_title = $product->name;
        }

        if($product->meta_description == null) {
            $product->meta_description = strip_tags($product->description);
        }

        if($product->meta_img == null) {
            //$product->meta_img = $product->thumbnail_img;
        }

        $product->pdf = $request->pdf;

        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $product->colors = json_encode($request->colors);
        }
        else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;

                $item['attribute_id'] = $no;

                $data = array();
                foreach ($request[$str] as $key => $eachValue) {
                    array_push($data, $eachValue);
                }

                $item['values'] = $data;
                array_push($choice_options, $item);
            }
        }

        foreach ($product->stocks as $key => $stock) {
            $stock->delete();
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        }
        else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);


        //combinations start
        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = array();
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        if(count($combinations[0]) > 0){
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination){
                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                    }
                    else{
                        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        }
                        else{
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
                if($product_stock == null){
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                }
                if(isset($request['price_'.str_replace('.', '_', $str)])) {
                    
                    $product_stock->variant = $str;
                    $product_stock->price = $request['price_'.str_replace('.', '_', $str)];
                    $product_stock->sku = $request['sku_'.str_replace('.', '_', $str)];
                    $product_stock->qty = $request['qty_'.str_replace('.', '_', $str)];
                    $product_stock->image = $request['img_'.str_replace('.', '_', $str)];

                    $product_stock->save();
                }
            }
        }
        else{
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product->id;
            $product_stock->variant     = '';
            $product_stock->price       = $request->unit_price;
            $product_stock->sku         = $request->sku;
            $product_stock->qty         = $request->current_stock;
            $product_stock->save();
        }

        $product->save();

        //Flash Deal
        if($request->flash_deal_id) {
            if($product->flash_deal_product){
                $flash_deal_product = FlashDealProduct::findOrFail($product->flash_deal_product->id);
                if(!$flash_deal_product) {
                    $flash_deal_product = new FlashDealProduct;
                }
            } else {
                $flash_deal_product = new FlashDealProduct;
            }
            
            $flash_deal_product->flash_deal_id = $request->flash_deal_id;
            $flash_deal_product->product_id = $product->id;
            $flash_deal_product->discount = $request->flash_discount;
            $flash_deal_product->discount_type = $request->flash_discount_type;
            $flash_deal_product->save();
//            dd($flash_deal_product);
    }

        //VAT & Tax
        if($request->tax_id) {
            ProductTax::where('product_id', $product->id)->delete();
            foreach ($request->tax_id as $key => $val) {
                $product_tax = new ProductTax;
                $product_tax->tax_id = $val;
                $product_tax->product_id = $product->id;
                $product_tax->tax = $request->tax[$key];
                $product_tax->tax_type = $request->tax_type[$key];
                $product_tax->save();
            }
        }

        // Product Translations
        $product_translation                = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
        $product_translation->name          = $request->name;
        $product_translation->unit          = $request->unit;
        $product_translation->description   = $request->description;
        $product_translation->save();

        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->archived = 1;
        $product->state = 'Inactive';
        $product->save();
        return redirect()->route('products.admin');
        // foreach ($product->product_translations as $key => $product_translations) {
        //     $product_translations->delete();
        // }

        // foreach ($product->stocks as $key => $stock) {
        //     $stock->delete();
        // }

        // if(Product::destroy($id)){
        //     Cart::where('product_id', $id)->delete();

        //     flash(translate('Product has been deleted successfully'))->success();

        //     Artisan::call('view:clear');
        //     Artisan::call('cache:clear');

        //     return back();
        // }
        // else{
        //     flash(translate('Something went wrong'))->error();
        //     return back();
        // }
    }

    public function visibility($id, $status) {
        // dd($status);
        $product = Product::findOrFail($id);
        $product->state = $status;
        $product->save();
        return redirect()->route('products.admin');
    }

    public function recover($id) {
        // dd($status);
        $product = Product::findOrFail($id);
        $product->archived = 0;
        $product->state = 'Active';
        $product->save();
        return redirect()->route('products.admin');
    }

    public function bulk_product_delete(Request $request) {
        if($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = substr($product_new->slug, 0, -5).Str::random(5);

        if($product_new->save()){
            foreach ($product->stocks as $key => $stock) {
                $product_stock              = new ProductStock;
                $product_stock->product_id  = $product_new->id;
                $product_stock->variant     = $stock->variant;
                $product_stock->price       = $stock->price;
                $product_stock->sku         = $stock->sku;
                $product_stock->qty         = $stock->qty;
                $product_stock->save();

            }

            flash(translate('Product has been duplicated successfully'))->success();
            if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
              if($request->type == 'In House')
                return redirect()->route('products.admin');
              elseif($request->type == 'Seller')
                return redirect()->route('products.seller');
              elseif($request->type == 'All')
                return redirect()->route('products.all');
            }
            else{
                if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null &&
                        \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                    $seller = Auth::user()->seller;
                    $seller->remaining_uploads -= 1;
                    $seller->save();
                }
                return redirect()->route('seller.products');
            }
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        if($product->save()){
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if($product->added_by == 'seller' && \App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
            $seller = $product->user->seller;
            if($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0){
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;

        if($product->added_by == 'seller' && \App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
            $seller = $product->user->seller;
            if($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0){
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if($product->save()){
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function updateSellerFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if($product->save()){
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = array();
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                foreach ($request[$name] as $key => $item) {
                    // array_push($data, $item->value);
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = array();
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                foreach ($request[$name] as $key => $item) {
                    // array_push($data, $item->value);
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }

    public function get_cat_parent(Request $request) {
        $parent_cat = Category::where('id', $request->cat_id)->get();
        dd($parent_cat);
    }

    public function add_fabric(Request $request) {
        $rec = new XztFabric();
        if($request->has('fabric_name')) {$rec->name = $request->fabric_name;}
        if($request->has('show_in_gallery')) {}
        if($request->hasFile('fabric_img')) {
            $file = $request->file('fabric_img');
            $name = $file->getClientOriginalName();
            $name = time().'-'.$name;
            $file->move(public_path().'/fabric/images', $name); 
            $rec->url = $name;
        }
        $rec->show_in_gallery = 'Yes';
        $rec->save();
        echo "<option value='".$rec->id."'>".$request->fabric_name."</option>";
    }


}