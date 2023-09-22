<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>User Name</th>
			<td>{{$user_detail->username}}</td>
		</tr>
		<tr>
			<th>Mobile Number</th>
			<td>{{$user_detail->contact_number}}</td>
		</tr>
		<tr>
			<th>Image</th>
			<td>
				@if(!empty($user_detail->avatar))
				<img src="{!! @$user_detail->avatar !== '' ? asset("storage/avatar/".@$user_detail->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle" style="height:30px; width:30px;">
				@else
				<img src="{!! asset('storage/avatar/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height:30px; width:30px;">
				@endif
			</td>
		</tr>
		<tr>
			<th>Email</th>
			<td>{{$user_detail->email}}</td>
		</tr>
		<tr>
			<th>User Type</th>
			<td>{{$user_detail->user_type}}</td>
		</tr>
		<tr>
			<th>Card Deatil</th>
			<td>
				<div class="col-md-8">
					<div class="payment-card">
						@if($card_detail->card_name == "Visa")
						<i class="fa fa-cc-visa payment-icon-big text-success"></i>
						@elseif($card_detail->card_name == "Mestro")
						<img src="{!! url('storage/card_details/Mestro.jpg') !!}" style="width: 77.14px; height: 60px;">
						@elseif($card_detail->card_name == "MasterCard")
						<i class="fa fa-cc-mastercard payment-icon-big text-warning"></i>
						@elseif($card_detail->card_name == "American Express")
						<i class="fa fa-cc-amex payment-icon-big text-success"></i>
						@elseif($card_detail->card_name == "RuPay")
						<img src="{!! url('storage/card_details/rupay.jpg') !!}" style="width: 77.14px; height: 60px;">
						@endif
						<h2>{{$card_detail->card_number}}</h2>
						<div class="row">
							<div class="col-sm-6">
								<small><strong>Expiry date:</strong> {{$card_detail->card_expiry_month}}/{{$card_detail->card_expiry_year}}</small>
							</div>
							<div class="col-sm-6 text-right">
								<small><strong>Name:</strong> {{$card_detail->card_holder_name}}</small>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($user_detail->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
		<tr>
			<td colspan="2"><center><b>User Diamond Detail</b></center></td>
		</tr>
	</table>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Diamond Packs</th>
		        <th>Diamond details</th>
		        <th>Diamond Rewards</th>
		        <th>Diamond Price</th>
		        <th>Diamond Status</th>
		    </tr>
		</thead>
		<tbody>
			<tr>
				<td>{{$diamond_detail->packs}}</td>
				<td>{{$diamond_detail->details}}</td>
				<td>{{$diamond_detail->rewards}}</td>
				<td>{{$diamond_detail->price}}</td>
				<td>
					@if($diamond_detail->status == "active")
					<span class="label label-success">Active</span>
					@else
					<span class="label label-danger">Block</span>
					@endif
				</td>
			</tr>
		</tbody>
	</table>
</div>