<div class="form-group  row {{ $errors->has('name') ? 'has-error' : '' }}">
    <div id="imagePreview" class="profile-image">
        @if(!empty($users->avatar))
        <img src="{!! @$users->avatar !== '' ? asset("storage/avatar/".@$users->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle">
        @else
        <img src="{!! asset('storage/avatar/default.png') !!}" alt="user-img" class="img-circle" accept="image/*">
        @endif
    </div>
    {!! Form::file('avatar',['id' => 'hidden','accept'=>"image/*"]) !!}
</div>
<div class="form-group last_name_block row {{ $errors->has('username') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>User Name</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('username',null,[
            'class' => 'form-control',
            'id'    => 'username',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('username') ? "".$errors->first('username')."" : '' }} </font>
            </span>
        </div>
    </div>
    
<div class="form-group last_name_block row {{ $errors->has('first_name') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>First Name</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('first_name',null,[
            'class' => 'form-control',
            'id'    => 'first_name',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('first_name') ? "".$errors->first('first_name')."" : '' }} </font>
            </span>
        </div>
    </div>

<div class="form-group last_name_block row {{ $errors->has('last_name') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Last Name</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('last_name',null,[
            'class' => 'form-control',
            'id'    => 'last_name',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('last_name') ? "".$errors->first('last_name')."" : '' }} </font>
            </span>
        </div>
    </div>
   

 <div class="form-group last_name_block row {{ $errors->has('contact_number') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Mobile Number</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('contact_number',null,[
            'class' => 'form-control',
            'id'    => 'contact_number',
            'maxlength' => '10'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('contact_number') ? "".$errors->first('contact_number')."" : '' }} </font>
            </span>
        </div>
    </div>

    <div class="form-group last_name_block row {{ $errors->has('email') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Email Id</strong> <span class="text-danger">*</span></label>
        <div class="col-sm-6">{!! Form::text('email',null,[
            'class' => 'form-control',
            'id'    => 'email',
            'maxlength' => '30'
            ]) !!}
            <span class="help-block">
                <font color="red"> {{ $errors->has('email') ? "".$errors->first('email')."" : '' }} </font>
            </span>
        </div>
    </div>

    <div class="form-group row password_block row {{ $errors->has('password') ? 'has-error' : '' }}">
            <label class="col-sm-3 col-form-label"><strong>Password</strong> <span class="text-danger">*</span></label>
            <div class="col-sm-6">
                {!! Form::password('password',[
                'class' => 'form-control',
                'id'    => 'password'
                ]) !!}
                <span class="help-block">
            <font color="red"> {{$errors->has('password') ? "".$errors->first('password')."" : '' }} </font>
        </span>
            </div>
        </div>

<div class="form-group row {{ $errors->has('status') ? 'has-error' : '' }}"><label class="col-sm-3 col-form-label"><strong>Status</strong></label>
    <div class="col-sm-6 inline-block">
        <div class="i-checks">
            <label>
                {{ Form::radio('status', 'active' ,true,['id'=> 'active']) }} <i></i> Active
            </label>
            <label>
                {{ Form::radio('status', 'inactive' ,false,['id' => 'inactive']) }}
                <i></i> InActive
            </label>
        </div>
        <span class="help-block">
            <font color="red">  {{ $errors->has('status') ? "".$errors->first('status')."" : '' }} </font>
        </span>
    </div>
</div>
<input type="hidden" name="user_type" value="user">
<br>
<br>
@if(!empty($users->card_details))
<?php $i ="1"; ?>
<div class="row">
@foreach($users->card_details as $card_user)
    <div class="col-md-4">
        <div class="payment-card">
            @if($card_user->card_name == "Visa")
            <i class="fa fa-cc-visa payment-icon-big text-success"></i>
            @elseif($card_user->card_name == "Mestro")
            <img src="{!! url('storage/card_details/Mestro.jpg') !!}" style="width: 77.14px; height: 60px;">
            @elseif($card_user->card_name == "MasterCard")
            <i class="fa fa-cc-mastercard payment-icon-big text-warning"></i>
            @elseif($card_user->card_name == "American Express")
            <i class="fa fa-cc-amex payment-icon-big text-success"></i>
            @elseif($card_user->card_name == "RuPay")
            <img src="{!! url('storage/card_details/rupay.jpg') !!}" style="width: 77.14px; height: 60px;">
            @endif
            <h2>{{$card_user->card_number}}</h2>
            <div class="row">
                <div class="col-sm-6">
                    <small><strong>Expiry date:</strong> {{$card_user->card_expiry_month}}/{{$card_user->card_expiry_year}}</small>
                </div>
                <div class="col-sm-6 text-right">
                    <small><strong>Name:</strong> {{$card_user->card_holder_name}}</small>
                </div>
            </div>
        </div>
    </div>
<?php $i ++; ?>
@endforeach
</div>
@else
@endif
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
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 15; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            //alert('asdas');
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment


                $(wrapper).append('<div class ="remove_test"><div class="hr-line-dashed"></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Card Number</strong></label><div class="col-sm-6"><input type ="text" name="card_number[]" class="form-control credit-card" id="credit-card" maxlength="19" onkeypress="return isNumberKey(event)" ></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Card Holder Name</strong></label><div class="col-sm-6"><input type ="text" name="card_holder_name[]" class="form-control" ></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Card Name</strong></label><div class="col-sm-6"><input type ="text" name="card_name[]" class="form-control" ></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Card Expiry Month</strong></label><div class="col-sm-6"><input type ="number" name="card_expiry_month[]" class="form-control w-45 date_abc date" id="date" data-provide="datepicker"></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Card Expiry Year</strong></label><div class="col-sm-6"><input type ="number" name="card_expiry_year[]" class="form-control year" id="year" data-provide="datepicker"></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>CVV</strong></label><div class="col-sm-6"><input type ="text" name="cvv[]" class="form-control" onkeypress="return isNumberKey(event)" id="cvv" maxlength="3" ></div></div><div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Billing Address</strong></label><div class="col-sm-6"><input type ="text" name="billing_address[]" class="form-control "></div></div> <div class="form-group  row "><label class="col-sm-3 col-form-label"><strong>Bank Name</strong></label><div class="col-sm-6"><input type ="text" name="bank_name[]" class="form-control" ></div></div> <div style="cursor:pointer;background-color:red;" class="remove_field btn btn-info">Remove</div></div>'); //add input box
                var date = new Date();
                date.setDate(date.getDate());

                $('.date').datepicker({
                    startDate: date,
                    format: "mm",
                    viewMode: "months",
                    minViewMode: "months"
                });


                var datee = new Date();
                date.setDate(date.getDate());

                $('.year').datepicker({
                    startDate: datee,
                    format: "yyyy",
                    viewMode: "years",
                    minViewMode: "years"
                });


                function isNumberKey(evt)
                {
                    var charCode = (evt.which) ? evt.which : event.keyCode
                    if (charCode > 31 && (charCode < 48 || charCode > 57))
                        return false;

                    return true;
                }

                $('.credit-card').on('keypress change blur', function () {
                    $(this).val(function (index, value) {

                        return value.replace(/[^a-z0-9]+/gi, '').replace(/(.{4})/g, '$1 ');
                    });
                });

                $('.credit-card').on('copy cut paste', function () {
                    setTimeout(function () {
                        $('.credit-card').trigger("change");
                    });
                });

            }
        });
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })
    });

    $(wrapper).on("click",".remove_field_id", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('.remove_test').remove(); x--;
    })
</script>
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


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
        $( "#passport_expiry_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: 0
        });
        $( "#passport_issue_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            maxDate: 0
        });


        $('#name').on('keyup onmouseout keydown keypress blur change', function (event) {
            var regex = new RegExp("^[a-zA-Z ._\\b\\t]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        });

        $('#mobile, #wpmobile').on('keyup onmouseout keydown keypress blur change', function (event) {
            var regex = new RegExp("^[0-9 ._\\b\\t]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }

        });
    });
</script>
@endsection