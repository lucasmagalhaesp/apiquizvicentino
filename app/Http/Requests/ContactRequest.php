<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
        return [
            "dados.name"    => "required",
            "dados.email"   => "required|email",
            "dados.subject" => "required",
            "dados.message" => "required",
        ];
    }

    public function messages()
    {
        return [
            "dados.name.required"       => "Favor informar o seu nome",
            "dados.email.required"      => "Favor informar o seu e-mail",
            "dados.email.email"         => "Favor informar um e-mail válido",
            "dados.subject.required"    => "Favor informar o assunto do contato",
            "dados.message.required"   => "Favor informar a mensagem desejada",
        ];
    }
}
