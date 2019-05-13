@extends('layouts.guest')

    @section('content')
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>Update</strong> Application</h2>
                    
                    <form role="form" action="{!! URL::to('/update') !!}" method="post" class="update-app-form" id="update-app-form" data-redirect="/login">
                        {!! csrf_field() !!}
                        <div class="form-group">
                        <input type="text" name="hostname" id="hostname" class="form-control text-input" placeholder="Hostname">
                        </div>
                        <div class="form-group">
                        <input type="text" name="mysql_database" id="mysql_database" class="form-control text-input" placeholder="MySQL Database">
                        </div>
                        <div class="form-group">
                        <input type="text" name="mysql_username" id="mysql_username" class="form-control text-input" placeholder="MySQL Username">
                        </div>
                        <div class="form-group">
                        <input type="text" name="mysql_password" id="mysql_password" class="form-control text-input" placeholder="MySQL Password">
                        </div>
                        <div class="form-group">
                        <input type="text" name="envato_username" id="envato_username" class="form-control text-input" placeholder="Envato Username">
                        </div>
                        <div class="form-group">
                        <input type="text" name="purchase_code" id="purchase_code" class="form-control text-input" placeholder="Purchase Code">
                        </div>
                        <div class="form-group">
                        <input type="email" name="email" id="email" class="form-control text-input" placeholder="Email">
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-unlock"></i> Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @stop