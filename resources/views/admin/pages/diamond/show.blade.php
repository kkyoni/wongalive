<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>Packs</th>
			<td>{{$diamond->packs}}</td>
		</tr>
		<tr>
			<th>Package Name</th>
			<td>{{$diamond->pack_name}}</td>
		</tr>
		<tr>
			<th>Details</th>
			<td>{{$diamond->details}}</td>
		</tr>
		<tr>
			<th>Rewards</th>
			<td>{{$diamond->rewards}}</td>
		</tr>
		<tr>
			<th>Price</th>
			<td>{{$diamond->price}}</td>
		</tr>
		<tr>
			<th>Image</th>
			<td>
				@if(!empty($diamond->avatar))
				<img src="{!! @$diamond->avatar !== '' ? asset("storage/diamond/".@$diamond->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle" style="height:30px; width:30px;">
				@else
				<img src="{!! asset('storage/diamond/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height:30px; width:30px;">
				@endif
			</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($diamond->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
	</table>
</div>