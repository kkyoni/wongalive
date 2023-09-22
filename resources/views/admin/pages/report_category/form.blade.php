<div class="form-group last_name_block row {{ $errors->has('category') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Report Category</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('category',null,[
            'class' => 'form-control',
            'id'    => 'category',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('category') ? "".$errors->first('category')."" : '' }} </font>
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
</script>
@endsection