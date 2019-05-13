<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $announcement = $this->route('announcement');
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'title' => 'required|unique:announcements',
                    'from_date' => 'required|date|before_equal:to_date',
                    'to_date' => 'required|date',
                    'description' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'title' => 'required|unique:announcements,title,'.$announcement->id.',id',
                    'from_date' => 'required|date|before_equal:to_date',
                    'to_date' => 'required|date',
                    'description' => 'required'
                ];
            }
            default:break;
        }
    }
}
