<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<th>Category Name</th>
			<td>{{$report_category->category}}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				@if ($report_category->status == "active") 
                   <span class="label label-success">Active</span>
                @else
                   <span class="label label-danger">Block</span>
                @endif
            </td>
		</tr>
	</table>
</div>