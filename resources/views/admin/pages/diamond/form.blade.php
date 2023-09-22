<div class="form-group  row {{ $errors->has('name') ? 'has-error' : '' }}">
    <div id="imagePreview" class="profile-image">
        @if(!empty($diamonds->avatar))
        <img src="{!! @$diamonds->avatar !== '' ? asset("storage/diamond/".@$diamonds->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle">
        @else
        <img src="{!! asset('storage/diamond/default.png') !!}" alt="user-img" class="img-circle" accept="image/*">
        @endif
    </div>
    {!! Form::file('avatar',['id' => 'hidden','accept'=>"image/*"]) !!}
</div>
<div class="form-group last_name_block row {{ $errors->has('packs') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Pack</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('packs',null,[
            'class' => 'form-control',
            'id'    => 'packs',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('packs') ? "".$errors->first('packs')."" : '' }} </font>
            </span>
        </div>
    </div>
<div class="form-group last_name_block row {{ $errors->has('packs') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Package Name</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('pack_name',null,[
            'class' => 'form-control',
            'id'    => 'pack_name',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('packs') ? "".$errors->first('packs')."" : '' }} </font>
            </span>
        </div>
    </div>
    
<div class="form-group last_name_block row {{ $errors->has('details') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Details</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('details',null,[
            'class' => 'form-control',
            'id'    => 'details',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('details') ? "".$errors->first('details')."" : '' }} </font>
            </span>
        </div>
    </div>

<div class="form-group last_name_block row {{ $errors->has('rewards') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Rewards</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('rewards',null,[
            'class' => 'form-control',
            'id'    => 'rewards',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('rewards') ? "".$errors->first('rewards')."" : '' }} </font>
            </span>
        </div>
    </div>
   

 <div class="form-group last_name_block row {{ $errors->has('price') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Price</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('price',null,[
            'class' => 'form-control',
            'id'    => 'price',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('price') ? "".$errors->first('price')."" : '' }} </font>
            </span>
        </div>
    </div>
<input type="hidden" name="status" value="active">
@section('styles')
<style type="text/css">
    .help-block {
        display: inline-block;
        margin-top: 5px;
        margin-bottom: 0px;
        margin-left: 5px;
    }
    .form-group {
        margin-bottom: 10px;
    }
    .form-control {
        font-size: 14px;
        font-weight: 500;
    }
    #imagePreview{
        width: 100%;
        height: 100%;
        text-align: center;
        margin:0 auto;
    }
    #hidden{
        display: none !important;
    }
    #imagePreview img {
        height: 150px;
        width: 150px;
        border: 3px solid rgba(0,0,0,0.4);
        padding: 3px;
    }

</style>

@endsection
@section('scripts')
 <link href="{{ asset('assets/admin/js/plugins/iCheck/icheck.min.js')}}" rel="stylesheet">
<script type="text/javascript">
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#imagePreview img').on('click',function(){
        $('input[type="file"]').trigger('click');
        $('input[type="file"]').change(function() {
            readURL(this);
        });
    });
</script>
@endsection