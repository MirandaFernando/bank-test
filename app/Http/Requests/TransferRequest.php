<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
        ];
    }

    public function messages()
    {
        return [
            'receiver_id.different' => 'Você não pode transferir para si mesmo.',
        ];
    }
}