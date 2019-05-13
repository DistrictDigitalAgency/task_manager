                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('email',trans('messages.email'),[])!!}
                                    <input type="email" class="form-control text-input" name="email" placeholder="{{trans('messages.email')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('username',trans('messages.username'),[])!!}
                                    <input type="text" class="form-control text-input" name="username" placeholder="{{trans('messages.username')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    {!! Form::label('first_name',trans('messages.first').' '.trans('messages.name'),[])!!}
                                    <input type="text" class="form-control text-input" name="first_name" placeholder="{{trans('messages.first').' '.trans('messages.name')}}">
                                </div>
                                <div class="col-sm-6">
                                    {!! Form::label('last_name',trans('messages.last').' '.trans('messages.name'),[])!!}
                                    <input type="text" class="form-control text-input" name="last_name" placeholder="{{trans('messages.last').' '.trans('messages.name')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('password',trans('messages.password'),[])!!}
                            <input type="password" class="form-control text-input @if(config('config.enable_password_strength_meter')) password-strength @endif" name="password" placeholder="{{trans('messages.password')}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('password_confirmation',trans('messages.confirm').' '.trans('messages.password'),[])!!}
                            <input type="password" class="form-control text-input" name="password_confirmation" placeholder="{{trans('messages.confirm').' '.trans('messages.password')}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('designation_id',trans('messages.designation'),[])!!}
                            {!! Form::select('designation_id', $designations,'',['class'=>'form-control show-tick','title' => trans('messages.select_one')])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('role_id',trans('messages.role'),[])!!}
                            {!! Form::select('role_id', $roles,'',['class'=>'form-control show-tick','title' => trans('messages.select_one')])!!}
                        </div>
                    </div>
                </div>
                {{ getCustomFields('user-registration-form') }}
                @if(Auth::check())
                <div class="form-group">
                    <input name="send_welcome_email" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1"> {{trans('messages.send')}} welcome email
                </div>
                @endif
                @if(config('config.enable_tnc') && !Auth::check())
                <div class="form-group">
                    <input name="tnc" type="checkbox" class="switch-input" data-size="mini" data-on-text="Yes" data-off-text="No" value="1"> I accept <a href="#" data-href="/terms-and-conditions" data-toggle="modal" data-target="#myModal">Terms & Conditions</a>.
                </div>
                @endif
                @if(config('config.enable_recaptcha') && !Auth::check())
                <div class="g-recaptcha" data-sitekey="{{config('config.recaptcha_key')}}"></div>
                <br />
                @endif
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-success pull-right"><i class="fa fa-lock"></i> {{trans('messages.create').' '.trans('messages.account')}}</button>
                    </div>
                </div>