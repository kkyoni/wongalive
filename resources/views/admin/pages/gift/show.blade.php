<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>Name</th>
			<td>{{$gift->name}}</td>
		</tr>
		<tr>
			<th>Price</th>
			<td>{{$gift->price}}</td>
		</tr>
		<tr>
			<th>Image</th>
			<td>
				@if(!empty($gift->avatar))
				<img src="{!! @$gift->avatar !== '' ? asset("storage/gift/".@$gift->avatar) : asset('storage/default.png') !!}" alt="user-img" class="img-circle" style="height:30px; width:30px;">
				@else
				<img src="{!! asset('storage/gift/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height:30px; width:30px;">
				@endif
			</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($gift->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
	</table>
</div>