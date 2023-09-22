<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>packs</th>
			<td>{{$cms_pages->title}}</td>
		</tr>
		<tr>
			<th>details</th>
			<td>{{$cms_pages->description}}</td>
		</tr>
			<tr>
			<th>status</th>
			<td>
				@if ($cms_pages->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
	</table>
</div>