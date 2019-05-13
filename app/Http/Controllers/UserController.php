<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Validator;
use File;
use Image;
use Entrust;
use App\Notifications\UserStatusChange;


class UserController extends Controller
{
    use BasicController;

    protected $form = 'user-form';

    public function index(){

        if(!Entrust::can('list-user'))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $data = array(
                    trans('messages.option'),
                    trans('messages.first').' '.trans('messages.name'),
                    trans('messages.last').' '.trans('messages.name'),
                    trans('messages.username'),
                    trans('messages.email'),
                    trans('messages.designation'),
                    trans('messages.department'),
                    trans('messages.location'),
                    trans('messages.role'),
                    trans('messages.date_of_joining'),
                    trans('messages.status')
                );

        $data = putCustomHeads($this->form, $data);

        $table_data['user-table'] = array(
                'source' => 'user',
                'title' => 'User List',
                'id' => 'user-table',
                'form' => 'user-filter-form',
                'data' => $data
            );

        $designations = \App\Designation::whereIn('id',getDesignation(\Auth::user()))->get()->pluck('full_designation','id')->all();
        $roles = \App\Role::whereIsHidden(0)->get()->pluck('name','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();

        $assets = ['datatable'];
        $menu = 'user';
        return view('user.index',compact('table_data','assets','menu','roles','designations','locations'));
    }

    public function lists(Request $request){
        if(!Entrust::can('list-user'))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $query = getAccessibleUser();

        if($request->has('role_id'))
            $query->whereHas('roles',function($q) use ($request){
                $q->whereIn('role_id',$request->input('role_id'));
            });

        if($request->has('designation_id'))
            $query->whereHas('profile',function($q) use ($request){
                $q->whereIn('designation_id',$request->input('designation_id'));
            });

        if($request->has('status'))
            $query->whereIn('status',$request->input('status'));

        if($request->has('date_start') && $request->has('date_end'))
            $query->whereBetween('created_at',[$request->input('date_start').' 00:00:00',$request->input('date_end').' 23:59:59']);

        $users = $query->get();

        $location = ($request->has('location_id')) ? \App\Location::whereIn('id',$request->input('location_id'))->get()->pluck('id')->all() : null;

        $col_ids = getCustomColId($this->form);
        $values = fetchCustomValues($this->form);
        $rows = array();

        foreach($users as $user){
            $row = array();

            $user_role = '<ol>';
            foreach($user->roles as $role)
                $user_role .= '<li>'.toWord($role->name).'</li>';
            $user_role .= '</ol>';

            $user_status = '';
            if($user->status == 'active')
                $user_status = '<span class="label label-success">'.trans('messages.active').'</span>';
            elseif($user->status == 'pending_activation')
                $user_status = '<span class="label label-warning">'.trans('messages.pending_activation').'</span>';
            elseif($user->status == 'pending_approval')
                $user_status = '<span class="label label-info">'.trans('messages.pending_approval').'</span>';
            elseif($user->status == 'banned')
                $user_status = '<span class="label label-danger">'.trans('messages.banned').'</span>';

            $user_location = getLocation($user->id);

            if(!$location || ($user_location && in_array($user_location->location_id, $location))){
                $row = array(
                    '<div class="btn-group btn-group-xs">'.
                    '<a href="/user/'.$user->id.'" class="btn btn-xs btn-default"> <i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="'.trans('messages.view').'"></i></a> '.
                    (($user->status == 'active' && Entrust::can('change-user-status')) ? '<a href="#" class="btn btn-xs btn-default" data-ajax="1" data-extra="&user_id='.$user->id.'&status=ban" data-source="/change-user-status"> <i class="fa fa-ban" data-toggle="tooltip" title="'.trans('messages.ban').' '.trans('messages.user').'"></i></a>' : '').
                    (($user->status == 'banned' && Entrust::can('change-user-status')) ? '<a href="#" class="btn btn-xs btn-default" data-ajax="1" data-extra="&user_id='.$user->id.'&status=active" data-source="/change-user-status"> <i class="fa fa-check" data-toggle="tooltip" title="'.trans('messages.activate').' '.trans('messages.user').'"></i></a>' : '').
                    (($user->status == 'pending_approval' && Entrust::can('change-user-status')) ? '<a href="#" class="btn btn-xs btn-default" data-ajax="1" data-extra="&user_id='.$user->id.'&status=approve" data-source="/change-user-status"> <i class="fa fa-check" data-toggle="tooltip" title="'.trans('messages.approve').' '.trans('messages.user').'"></i></a>' : '').
                    (Entrust::can('delete-user') ? delete_form(['user.destroy',$user->id]) : '').
                    '</div>',
                    $user->Profile->first_name,
                    $user->Profile->last_name,
                    $user->username.' '.(($user->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : ''),
                    $user->email,
                    $user->Profile->Designation->name,
                    $user->Profile->Designation->Department->name,
                    ($user_location) ? $user_location->Location->name : '',
                    $user_role,
                    showDate($user->created_at),
                    $user_status
                    );
                $id = $user->id;

                foreach($col_ids as $col_id)
                    array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
                $rows[] = $row;
            }
        }
        $list['aaData'] = $rows;
        return json_encode($list);
    }

    public function show(User $user){

        if(!$this->userAccessible($user->id))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $roles = \App\Role::whereIsHidden(0)->get()->pluck('name','id')->all();
        $all_user_roles = $user->Roles;
        $user_roles = array();
        foreach($all_user_roles as $user_role)
            $user_roles[] = $user_role->id;

        $designations = \App\Designation::whereIn('id',getDesignation(\Auth::user()))->get()->pluck('full_designation','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();
        $templates = \App\Template::whereCategory('user')->pluck('name','id')->all();
        $custom_social_field_values = getCustomFieldValues('user-social-form',$user->id);
        $custom_register_field_values = getCustomFieldValues('user-registration-form',$user->id);

        $assets = ['summernote'];
        $menu = 'user';

        return view('user.show',compact('user','roles','user_roles','templates','custom_social_field_values','custom_register_field_values','assets','menu','locations','designations'));
    }

    public function changeStatus(Request $request){

        $user_id = $request->input('user_id');
        $status = $request->input('status');

        $user = \App\User::find($user_id);
        if(!$user)
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        if(!Entrust::can('change-user-status') || $user->hasRole(DEFAULT_ROLE)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.permission_denied'));
        }

        if($status == 'ban' && $user->status != 'active')
            return redirect('/user')->withErrors(trans('messages.invalid_link'));
        elseif($status == 'approve' && $user->status != 'pending_approval')
            return redirect('/user')->withErrors(trans('messages.invalid_link'));
        elseif($status == 'active' && $user->status != 'banned')
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        if($status == 'ban')
            $user->status = 'banned';
        elseif($status == 'approve' || $status == 'active')
            $user->status  = 'active';

        $user->save();
        $user->notify(new UserStatusChange($user));

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.status').' '.trans('messages.updated'));
    }

    public function avatar(Request $request, $id){

        if(!Entrust::can('update-user') || !$this->userAccessible($id))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $user = \App\User::find($id);

        if(!$user)
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        $profile = $user->Profile;

        $validation = Validator::make($request->all(),[
            'avatar' => 'image'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }

            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $filename = uniqid();

        if ($request->hasFile('avatar') && $request->input('remove_avatar') != 1){
            if(File::exists(config('constant.upload_path.avatar').config('config.avatar')))
                File::delete(config('constant.upload_path.avatar').config('config.avatar'));
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $file = $request->file('avatar')->move(config('constant.upload_path.avatar'), $filename.".".$extension);
            $img = Image::make(config('constant.upload_path.avatar').$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(config('constant.upload_path.avatar').$filename.".".$extension);
            $profile->avatar = $filename.".".$extension;
        } elseif($request->input('remove_avatar') == 1){
            if(File::exists(config('constant.upload_path.avatar').config('config.avatar')))
                File::delete(config('constant.upload_path.avatar').config('config.avatar'));
            $profile->avatar = null;
        }

        $profile->save();

        $this->logActivity(['module' => 'profile','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.profile').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.profile').' '.trans('messages.updated'));
    }

    public function profileUpdate(Request $request, $id){
        
        if(!Entrust::can('update-user') || !$this->userAccessible($id))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $user = \App\User::find($id);

        if(!$user)
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        $profile = $user->Profile;

        $profile->fill($request->all());
        $profile->designation_id = $request->input('designation_id');

        $profile->date_of_birth = ($request->input('date_of_birth')) ? : null;
        $profile->save();

        if($request->has('role_id') && !$user->hasRole(DEFAULT_ROLE)){
            $user->roles()->sync($request->input('role_id'));
        }

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.profile').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.profile').' '.trans('messages.updated'));
    }

    public function socialUpdate(Request $request, $id){
        
        if(!Entrust::can('update-user') || !$this->userAccessible($id))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $validation = validateCustomField('user-social-form',$request);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $user = \App\User::find($id);

        if(!$user)
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        $profile = $user->Profile;

        $data = $request->all();
        $profile->fill($data);
        $profile->save();
        storeCustomField('user-social-form',$user->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.profile').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.profile').' '.trans('messages.updated'));
    }

    public function customFieldUpdate(Request $request,$id){

        if(!Entrust::can('update-user') || !$this->userAccessible($id))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $validation = validateCustomField('user-registration-form',$request);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $user = \App\User::find($id);

        if(!$user)
            return redirect('/user')->withErrors(trans('messages.invalid_link'));

        $data = $request->all();
        storeCustomField('user-registration-form',$user->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.custom').' '.trans('messages.field').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.custom').' '.trans('messages.field').' '.trans('messages.updated'));
    }

    public function email(Request $request, $id){

        if(!Entrust::can('email-user') || (!Entrust::hasRole(DEFAULT_ROLE) && $user->hasRole(DEFAULT_ROLE)) )
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        $validation = Validator::make($request->all(),[
            'subject' => 'required',
            'body' => 'required'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $user = User::find($id);
        $mail['email'] = $user->email;
        $mail['subject'] = $request->input('subject');
        $body = $request->input('body');

        \Mail::send('emails.email', compact('body'), function($message) use ($mail){
            $message->to($mail['email'])->subject($mail['subject']);
        });
        $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));

        $this->logActivity(['module' => 'employee','unique_id' => $user->id,'activity' => 'activity_mail_sent']);
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.mail').' '.trans('messages.sent'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.mail').' '.trans('messages.sent'));
    }

    public function changePassword(){
        return view('auth.change_password');
    }

    public function doChangePassword(Request $request){
        if(!getMode()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.disable_message'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.disable_message'));
        }

        $credentials = $request->only(
                'new_password', 'new_password_confirmation'
        );
        $validation_messages = [
            'password.regex' => trans('messages.password_alphanumeric'),
        ];

        $validation = Validator::make($request->all(),[
            'old_password' => 'required|valid_password',
            'new_password' => 'required|confirmed|different:old_password|min:6|regex:/^[a-zA-Z0-9_\.\-]*$/',
            'new_password_confirmation' => 'required|different:old_password|same:new_password'
        ],$validation_messages);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $user = \Auth::user();
        
        $user->password = bcrypt($credentials['new_password']);
        $user->save();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.password').' '.trans('messages.changed'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        
        return redirect()->back()->withErrors(trans('messages.password').' '.trans('messages.changed'));
    }

    public function forceChangePassword($user_id,Request $request){
        if(!Entrust::can('reset-user-password'))
            return redirect('/home')->withErrors(trans('messages.permission_denied'));

        if($user_id == \Auth::user()->id){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.invalid_link'));
        }

        if(!getMode()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.disable_message'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.disable_message'));
        }

        $credentials = $request->only(
                'new_password', 'new_password_confirmation'
        );

        $validation = Validator::make($request->all(),[
            'new_password' => 'required|confirmed|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $user = User::find($user_id);
        
        $user->password = bcrypt($credentials['new_password']);
        $user->save();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.password').' '.trans('messages.changed'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        
        return redirect()->back()->withErrors(trans('messages.password').' '.trans('messages.changed'));
    }

    public function destroy(User $user,Request $request){

        if(!Entrust::can('delete-user') || !$this->userAccessible($user->id) || $user->is_hidden){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
          return redirect('/home')->withErrors(trans('messages.permission_denied'));
        }

        if($user->id == \Auth::user()->id){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/home')->withErrors(trans('message.unable_to_delete_yourself'));
        }

        deleteCustomField($this->form, $user->id);
        $this->logActivity(['module' => 'user','unique_id' => $user->id,'activity' => 'activity_deleted']);

        $user->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.user').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/home')->withSuccess(trans('messages.user').' '.trans('messages.deleted'));
    }
}