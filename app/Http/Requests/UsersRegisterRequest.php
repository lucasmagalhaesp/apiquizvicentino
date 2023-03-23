<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRegisterRequest extends FormRequest
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
            "dados.name"            => "required",
            "dados.email"           => "required|email|unique:users,email",
            "dados.password"        => "required|min:6",
            "dados.state"           => "required|size:2",
            "dados.city"            => "required|not_in:Selecione sua cidade",
            "dados.confirmPassword" => "required|same:dados.password"
        ];
    }

    public function messages()
    {
        return [
            "dados.name.required"            => "Informe o seu nome",
            "dados.email.required"           => "Informe seu e-mail",
            "dados.email.email"              => "E-mail inválido",
            "dados.email.unique"             => "E-mail já cadastrado. Utilize outro para realizar um novo cadastro ou recupere sua senha para utilizar esse mesmo e-mail",
            "dados.password.required"        => "Crie uma senha",
            "dados.password.min"             => "Sua senha deve ter no mínimo 6 caracteres",
            "dados.state.required"           => "Informe a UF",
            "dados.state.size"               => "UF inválida",
            "dados.city.required"            => "Informe sua cidade",
            "dados.city.not_in"              => "Informe sua cidade",
            "dados.confirmPassword.required" => "Confirme sua senha",
            "dados.confirmPassword.same"     => "Senhas não coincidem",
        ];
    }
}
