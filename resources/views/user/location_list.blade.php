	@foreach($user_locations as $user_location)
	<tr>
		<td>{{showDate($user_location->from_date).(($user_location->to_date) ? (' to '.showDate($user_location->to_date)) : '')}}</td>
        <td>{{$user_location->Location->name}}</td>
        <td>{{$user_location->description}}</td>
		<td>
			<div class="btn-group btn-group-xs">
				<a href="#" data-href="/user-location/{{$user_location->id}}/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="{{trans('messages.edit')}}"></i></a>
                {!! delete_form(['user-location.destroy',$user_location->id],['table-refresh' => 'user-location-table']) !!}
			</div>
		</td>
	</tr>
	@endforeach