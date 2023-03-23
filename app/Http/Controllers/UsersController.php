<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UsersRegisterRequest;
use App\Http\Requests\UsersLoginRequest;
use App\User;
use Hash;

class UsersController extends Controller
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function index()
    {
        return response()->json(["success" => true, "users" => $this->model->orderBy("id", "desc")->get()]);   
    }

    public function store(UsersRegisterRequest $request)
    {
        $this->model->name = $request->dados["name"];
        $this->model->email = $request->dados["email"];
        $this->model->password = Hash::make($request->dados["password"]);
        $this->model->state = $request->dados["state"];
        $this->model->city = $request->dados["city"];

        try{
            $this->model->save();
        }catch(\Excepition $e){
            return response()->json(["success" => false, "msg" => "Erro ao cadastrar usuário: ".$e->getMessage()]); 
        }

        return response()->json(["success" => true]);
    }

    public function show($id)
    {
        return response()->json(["success" => true, "data" => $this->model->find($id)]);
    }

    public function login(UsersLoginRequest $request){
        return response()->json(["success" => true, "data" => $request->all()]);
    }
}
