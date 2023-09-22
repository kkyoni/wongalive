<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>User Name</th>
			<td>{{$user->username}}</td>
		</tr>
		<tr>
			<th>First Name</th>
			<td>{{$user->first_name}}</td>
		</tr>
		@if(!empty($user->last_name))
		<tr>
			<th>Last Name</th>
			<td>{{$user->last_name}}</td>
		</tr>
		@endif
		<tr>
			<th>Mobile Number</th>
			<td>{{$user->contact_number}}</td>
		</tr>
		<tr>
			<th>Image</th>
			<td>
				@if(!empty($user->avatar))
				<img src="{!! @$user->avatar !== '' ? asset("storage/avatar/".@$user->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle" style="height:30px; width:30px;">
				@else
				<img src="{!! asset('storage/avatar/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height:30px; width:30px;">
				@endif
			</td>
		</tr>
		<tr>
			<th>Email</th>
			<td>{{$user->email}}</td>
		</tr>
		<tr>
			<th>User Type</th>
			<td>{{$user->user_type}}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($user->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
		 <tr>
		 	<td colspan="2"><center><b>User Wallet Detail</b></center></td>
		 </tr>
		 <tr>
			<th>Total Diamonds</th>
			<td><i class="fa fa-diamond"> {{$user->diamond}}</i></td>
		</tr>
	</table>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Follow</th>
		        <th>Following</th>
		    </tr>
		</thead>
		<tbody>
			<tr>
				<td><i class="fa fa-users"></i> {{$follow_count}}</td>
				<td><i class="fa fa-users"></i> {{$following_count}}</td>
			</tr>
		</tbody>
	</table>
</div>