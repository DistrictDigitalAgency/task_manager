<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\LocalizationRequest;
use File;
use Entrust;
use Validator;

Class LocalizationController extends Controller{
    use BasicController;

	public function __construct()
	{
		$this->middleware('feature_available:multilingual');
	}

	public function index(){

		$table_data['localization-table'] = array(
			'source' => 'localization',
			'title' => 'Localization List',
			'id' => 'localization_table',
			'data' => array(
        		trans('messages.option'),
        		trans('messages.locale'),
        		trans('messages.name'),
        		trans('messages.percentage').' '.trans('messages.translation')
        		)
			);

		$assets = ['datatable'];

		return view('localization.index',compact('table_data','assets'));
	}

	public function lists(Request $request){

        $localizations = config('localization');
		$translation_count = count(config('translation'));
		$rows = array();

        foreach($localizations as $locale => $localization){

			$trans = File::getRequire(base_path().'/resources/lang/'.$locale.'/messages.php');
    		$percentage = ($translation_count) ? round(((count($trans)*100)/$translation_count),2) : 0;

			$rows[] = array(
				'<div class="btn-group btn-group-xs">'.
				'<a href="/localization/'.$locale.'" class="btn btn-default btn-xs md-trigger"> <i class="fa fa-arrow-circle-right" title="'.trans('messages.view').'" data-toggle="tooltip"></i></a> '.
				'<a href="#" data-href="/localization/'.$locale.'/edit" class="btn btn-default btn-xs md-trigger" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" title="'.trans('messages.edit').'" data-toggle="tooltip"></i></a> '.
				delete_form(['localization.destroy',$locale]).'</div>',
				$locale,
				$localization['localization'],
				$percentage.' % '.trans('messages.translation')
				);	
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function plugin(Request $request,$locale){

		$localization = config('localization');
		$localization[$locale] = array(
			'localization' => config('localization.'.$locale.'.localization'),
			'calendar' => $request->input('calendar'),
			'datatable' => $request->input('datatable'),
			'datetimepicker' => $request->input('datetimepicker'),
			'datepicker' => $request->input('datepicker'),
			'validation' => $request->input('validation'),
			);

		$filename = base_path().config('constant.path.localization');
		File::put($filename,var_export($localization, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		$this->logActivity(['module' => 'localization','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.localization').' '.trans('messages.plugin').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.localization').' '.trans('messages.plugin').' '.trans('messages.updated'));
	}

	public function show($locale){

		if(!array_key_exists($locale, config('localization')))
			return redirect()->back()->withErrors(trans('messages.invalid_link'));

		$localization = config('localization.'.$locale);

		$words = config('translation');
		$modules = array();
		foreach($words as $word)
			$modules[] = $word['module'];

		$word_count = array_count_values($modules);

		$modules = array_unique($modules);
		asort($modules);

		asort($words);

		$translation = File::getRequire(base_path().'/resources/lang/'.$locale.'/messages.php');

		return view('localization.show',compact('localization','words','translation','locale','modules','word_count'));
	}

	public function create(){
	}

	public function edit($locale){

		if(!array_key_exists($locale, config('localization')))
            return view('global.error',['message' => trans('messages.invalid_link')]);

		return view('localization.edit',compact('locale'));
	}

	
	public function update(LocalizationRequest $request, $locale){

		$localizations = config('localization');
		if(!array_key_exists($locale, $localizations)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$localizations[$locale] = [
							'localization' => $request->input('name'),
							'datatable' => $request->input('datatable'),
							'calendar' => $request->input('calendar')
							];
		$filename = base_path().config('constant.path.localization');
		File::put($filename,var_export($localizations, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');
		
		$this->logActivity(['module' => 'localization','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.localization').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/localization')->withSuccess(trans('messages.localization').' '.trans('messages.updated'));
	}

	public function store(LocalizationRequest $request){

        $localizations = config('localization');

		if(array_key_exists($request->input('locale'), $localizations)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.localization_already_added'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.localization_already_added'));
		}

		$localizations[$request->input('locale')] = [
			'localization' => $request->input('name')
			];
		$filename = base_path().config('constant.path.localization');

		File::put($filename,var_export($localizations, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		File::copyDirectory(base_path().'/resources/lang/en', base_path().'/resources/lang/'.$request->input('locale'));
		$filename = base_path().'/resources/lang/'.$request->input('locale').'/messages.php';
		File::put($filename,'<?php return array();');

		$this->logActivity(['module' => 'localization','activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.localization').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.localization').' '.trans('messages.added'));	
	}

	public function changeLocalization($locale,Request $request){

        $localizations = config('localization');
		if(!array_key_exists($locale, $localizations))
			return redirect()->back()->withErrors(trans('messages.invalid_link'));

		session(['localization' => $locale]);
		\App::setLocale($locale);

		$this->logActivity(['module' => 'localization','activity' => 'activity_switched']);

		return redirect()->back()->withSuccess(trans('messages.localization').' '.trans('messages.switched'));
	}

	public function addWords(Request $request){

		if(!getMode())
			return redirect()->back()->withErrors(trans('messages.disable_message'));
		
		$validator = Validator::make($request->all(),[
		    	'key' => 'required',
		    	'text' => 'required',
		    	'module' => 'required']
		);

		if ($validator->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validator->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors($validator->messages()->first());
		}

		$data = $request->all();

		$translation = config('translation');
		$word_array = array();
		foreach(config('translation') as $word => $value){
			$word_array[] = $word;
		}
		if(in_array($data['key'],$word_array)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.word_already_added'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors(trans('messages.word_already_added'));
		}

		$translation[$data['key']] = array('value' => $data['text'],'module' => $data['module']);
		$filename = base_path().config('constant.path.translation');
		File::put($filename,var_export($translation, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		$this->logActivity(['module' => 'localization','activity' => 'activity_added']);
	
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.word_or_sentence').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.word_or_sentence').' '.trans('messages.added'));	
	}

	public function updateTranslation(Request $request, $locale){
		$array_input = $request->all();

		if(!array_key_exists($locale,config('localization'))){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$localization_translation = File::getRequire(base_path().'/resources/lang/'.$locale.'/messages.php');
		foreach($array_input as $key => $value)
			if($key != '_token' && $key != '_method' && $key != 'module' && $value != '' && $key != 'ajax_submit')
				$localization_translation[$key] = $value;

		$filename = base_path().'/resources/lang/'.$locale.'/messages.php';
		File::put($filename,var_export($localization_translation, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		$this->logActivity(['module' => 'localization','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.localization').' '.trans('messages.translation').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/localization/'.$locale.'#_'.$request->input('module'))->withSuccess(trans('messages.localization').' '.trans('messages.translation').' '.trans('messages.updated'));	
	}

	public function destroy($locale,Request $request){

        $localizations = config('localization');
		if(!array_key_exists($locale, $localizations)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		if($locale == 'en'){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.cannot_delete_primary_localization'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/localization')->withErrors(trans('messages.cannot_delete_primary_localization'));
		}

		if(session('localization') == $locale){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.cannot_delete_default_localization'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/localization')->withErrors(trans('messages.cannot_delete_default_localization'));
		}

		$result = File::deleteDirectory(base_path().'/resources/lang/'.$locale);
		unset($localizations[$locale]);
		$filename = base_path().config('constant.path.localization');
		File::put($filename,var_export($localizations, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		$this->logActivity(['module' => 'localization','activity' => 'activity_deleted']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.localization').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/localization')->withSuccess(trans('messages.localization').' '.trans('messages.deleted'));
	}
}
?>