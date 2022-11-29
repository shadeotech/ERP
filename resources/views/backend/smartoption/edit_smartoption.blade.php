@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Edit Smartoption</h5>
</div>

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('smartoptions.update', $smartoption->id) }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Name</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Smartoption Name" id="name" name="name" class="form-control" value="{{$smartoption->name}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Price</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Price" id="price" name="price" class="form-control" value="{{$smartoption->price}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Image</label>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <div class="form-group form-check ">
                            <input type="checkbox" class="form-check-input" value="Active" id="state" name="state" @if($smartoption->state == 'Active') checked @endif>
                            <label class="form-check-label pt-1" for="state" style="font-size:0.75rem;">Active</label>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
