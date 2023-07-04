<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class RequestsRequest extends FormRequest
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'type' => 'required',
            'title' => 'required',
            'type_w' => 'required_if:type,warranty',
            'file1' => 'required_if:type,warranty|file|mimes:doc,docx,pdf,zip,png,jpg|max:2048',
            'file2' => 'required_if:type,warranty|file|mimes:doc,docx,pdf,zip,png,jpg|max:2048',
            'file3' => 'required_if:type,warranty|file|mimes:doc,docx,pdf,zip,png,jpg|max:2048',
            'licenses' => 'required_if:type,warranty',
            'register_doc' => 'required_if:type,warranty',
            'signatory' => 'required_if:type,warranty',
            'knowledge' => 'required_if:type,warranty',
            'resume' => 'required_if:type,warranty',
            'loans' => 'required_if:type,warranty',
            'statements' => 'required_if:type,warranty',
            'balances' => 'required_if:type,warranty',
            'catalogs' => 'required_if:type,warranty',
            'insurances' => 'required_if:type,warranty',
            'invoices' => 'required_if:type,warranty',
            'bills' => 'required_if:type,warranty',
            'type_f' => 'required_if:type,facilities',
            'places' => 'required_if:type,facilities',
            'history' => 'required_if:type,facilities',
            'activity' => 'required_if:type,facilities',
            'is_knowledge' => 'required_if:type,facilities',
            'confirmation' => 'nullable|date',
            'expiration' => 'nullable|date',
            'area' => 'nullable'
        ];
    }

    public function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->id;
        throw new HttpResponseException(response()->json(
            [
                'id' => $id,
                'success' => false,
                'message' => $validator->errors()
            ],
            422)
        );
    }
}
