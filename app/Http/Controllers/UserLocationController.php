<?php
namespace App\Http\Controllers;
use App\UserLocation;
use Illuminate\Http\Request;
use App\Http\Requests\UserLocationRequest;

Class UserLocationController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $user = \App\User::find($request->input('user_id'));

        if(!$user)
            return null;

        $user_locations = $user->UserLocation->sortBy('from_date');
        return view('user.location_list',compact('user_locations'))->render();
    }

    public function store(UserLocationRequest $request, $id){
        $user = \App\User::find($id);

        if(!$user){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user')->withErrors(trans('messages.invalid_link'));
        }

        if(!$this->userAccessible($user->id)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user')->withErrors(trans('messages.permission_denied'));
        }

        if(UserLocation::whereUserId($id)->whereNull('to_date')->count()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.already_undefined_end_date'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$id.'#location')->withErrors(trans('messages.already_undefined_end_date'));
        }

        $previous_location = UserLocation::whereUserId($id)->first();

        if($previous_location && $request->input('from_date') <= $previous_location->from_date){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.back_date_entry'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$id.'#location')->withErrors(trans('messages.back_date_entry'));
        }

        if($request->has('to_date'))
            $location = UserLocation::whereUserId($id)
                ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                    $query->where('from_date','<=',$request->input('from_date'))
                    ->where('to_date','>=',$request->input('from_date'));
                    })->orWhere(function ($query) use($request) {
                        $query->where('from_date','<=',$request->input('to_date'))
                            ->where('to_date','>=',$request->input('to_date'));
                    });})->count();
        else
            $location = UserLocation::whereUserId($id)->where('from_date','<=',$request->input('from_date'))
                        ->where('to_date','>=',$request->input('from_date'))->count();

        if($location){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.entry_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$id.'#location')->withErrors(trans('messages.entry_already_defined'));
        }

        $user_location = new UserLocation;
        $data = $request->all();
        $data['user_id'] = $id;
        $data['to_date'] = ($request->has('to_date')) ? $request->input('to_date') : null;
        $data['description'] = $request->input('description');
        $user_location->fill($data)->save();

        $current_location = getLocation($id);
        $profile = $user->Profile;
        if($current_location){
            $profile->location_id = $current_location->Location->id;
            $profile->save();
        }

        $this->logActivity(['module' => 'user_location','activity' => 'activity_added','secondary_id' => $user->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/user/'.$id.'#location')->withSuccess(trans('messages.location').' '.trans('messages.added'));
    }

    public function edit(UserLocation $user_location){
        $user = \App\User::find($user_location->user_id);
        $locations = \App\Location::all()->pluck('name','id')->all();

        if(!$this->userAccessible($user->id))
            return view('global.error',['message' => trans('messages.permission_denied')]);

        return view('user.edit_location',compact('user_location','locations','user'));
    }

    public function update(UserLocationRequest $request, UserLocation $user_location){

        if(UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->whereNull('to_date')->count()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.already_undefined_end_date'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$user_location->user_id.'#location')->withErrors(trans('messages.already_undefined_end_date'));
        }

        $previous_location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->first();

        if($previous_location && $request->input('from_date') <= $previous_location->from_date){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.back_date_entry'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$user_location->user_id.'#location')->withErrors(trans('messages.back_date_entry'));
        }

        if($request->has('to_date'))
            $location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)
                ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                    $query->where('from_date','<=',$request->input('from_date'))
                    ->where('to_date','>=',$request->input('from_date'));
                    })->orWhere(function ($query) use($request) {
                        $query->where('from_date','<=',$request->input('to_date'))
                            ->where('to_date','>=',$request->input('to_date'));
                    });})->count();
        else
            $location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->where('from_date','<=',$request->input('from_date'))
                        ->where('to_date','>=',$request->input('from_date'))->count();

        if($location){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.entry_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/user/'.$user_location->user_id.'#location')->withErrors(trans('messages.entry_already_defined'));
        }

        $data = $request->all();
        $data['to_date'] = ($request->has('to_date')) ? $request->input('to_date') : null;
        $date['description'] = $request->input('description');
        $user_location->fill($data)->save();

        $current_location = getLocation($user_location->User->id);
        $profile = $user_location->User->Profile;
        if($current_location){
            $profile->location_id = $current_location->Location->id;
            $profile->save();
        }
        
        $this->logActivity(['module' => 'user_location','activity' => 'activity_updated','secondary_id' => $user_location->user_id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/user/'.$user_location->user_id.'#location')->withSuccess(trans('messages.location').' '.trans('messages.updated'));
    }

    public function destroy(UserLocation $user_location,Request $request){
        if(!$this->userAccessible($user_location->User->id)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/home')->withErrors(trans('messages.permission_denied'));
        }

        $this->logActivity(['module' => 'user_location','activity' => 'activity_deleted','secondary_id' => $user_location->user_id]);
        $user_location->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.location').' '.trans('messages.deleted'));
    }
}
?>