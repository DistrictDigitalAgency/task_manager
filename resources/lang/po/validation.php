<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => trans('messages.validation_accepted'),
    'active_url'           => trans('messages.validation_active_url'),
    'after'                => trans('messages.validation_after'),
    'alpha'                => trans('messages.validation_alpha'),
    'alpha_dash'           => trans('messages.validation_alpha_dash'),
    'alpha_num'            => trans('messages.validation_alpha_num'),
    'array'                => trans('messages.validation_array'),
    'before'               => trans('messages.validation_before'),
    'between'              => [
        'numeric' => trans('messages.validation_between_numeric'),
        'file'    => trans('messages.validation_between_file'),
        'string'  => trans('messages.validation_between_string'),
        'array'   => trans('messages.validation_between_array'),
    ],
    'boolean'              => trans('messages.validation_boolean'),
    'confirmed'            => trans('messages.validation_confirmed'),
    'date'                 => trans('messages.validation_date'),
    'date_format'          => trans('messages.validation_date_format'),
    'different'            => trans('messages.validation_different'),
    'digits'               => trans('messages.validation_digits'),
    'digits_between'       => trans('messages.validation_digits_between'),
    'dimensions'           => trans('messages.validation_dimensions'),
    'distinct'             => trans('messages.validation_distinct'),
    'email'                => trans('messages.validation_email'),
    'exists'               => trans('messages.validation_exists'),
    'file'                 => trans('messages.validation_file'),
    'filled'               => trans('messages.validation_filled'),
    'image'                => trans('messages.validation_image'),
    'in'                   => trans('messages.validation_in'),
    'in_array'             => trans('messages.validation_in_array'),
    'integer'              => trans('messages.validation_integer'),
    'ip'                   => trans('messages.validation_ip'),
    'json'                 => trans('messages.validation_json'),
    'max'                  => [
        'numeric' => trans('messages.validation_max_numeric'),
        'file'    => trans('messages.validation_max_file'),
        'string'  => trans('messages.validation_max_string'),
        'array'   => trans('messages.validation_max_array'),
    ],
    'mimes'                => trans('messages.validation_mimes'),
    'min'                  => [
        'numeric' => trans('messages.validation_min_numeric'),
        'file'    => trans('messages.validation_min_file'),
        'string'  => trans('messages.validation_min_string'),
        'array'   => trans('messages.validation_min_array'),
    ],
    'not_in'               => trans('messages.validation_not_in'),
    'numeric'              => trans('messages.validation_numeric'),
    'present'              => trans('messages.validation_present'),
    'regex'                => trans('messages.validation_regex'),
    'required'             => trans('messages.validation_required'),
    'required_if'          => trans('messages.validation_required_if'),
    'required_unless'      => trans('messages.validation_required_unless'),
    'required_with'        => trans('messages.validation_required_with'),
    'required_with_all'    => trans('messages.validation_required_with_all'),
    'required_without'     => trans('messages.validation_required_without'),
    'required_without_all' => trans('messages.validation_required_without_all'),
    'same'                 => trans('messages.validation_same'),
    'size'                 => [
        'numeric' => trans('messages.validation_size_numeric'),
        'file'    => trans('messages.validation_size_file'),
        'string'  => trans('messages.validation_size_string'),
        'array'   => trans('messages.validation_size_array'),
    ],
    'string'               => trans('messages.validation_string'),
    'timezone'             => trans('messages.validation_timezone'),
    'unique'               => trans('messages.validation_unique'),
    'url'                  => trans('messages.validation_url'),
    "before_equal"         => trans('messages.validation_before_equal'),
    "after_equal"         => trans('messages.validation_after_equal'),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => array(
        'old_password' => array(
            'valid_password' => trans('messages.valid_password'),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
