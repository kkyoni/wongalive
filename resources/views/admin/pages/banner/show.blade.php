<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>Banner Name</th>
			<td>{{$banner->banner_name}}</td>
		</tr>
		<tr>
			<th>Details</th>
			<td>{{$banner->details}}</td>
		</tr>
		<tr>
		<tr>
			<th>Status</th>
			<td>{{$banner->status}}</td>
		</tr>
		<tr>
			<th>Banne Image</th>
			<td>
				@if(!empty($banner->banner_image))
				<img src="{!! @$banner->banner_image !== '' ? asset("storage/banner/".@$banner->banner_image) : asset('storage/default.png') !!}" alt="user-img" class="img-circle" style="height:30px; width:30px;">
				@else
				<img src="{!! asset('storage/banner/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height:30px; width:30px;">
				@endif
			</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($banner->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
	</table>
</div>