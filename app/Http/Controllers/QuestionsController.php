<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuestionsController extends Controller
{
    private $model;

    public function __construct(Question $question)
    {
        $this->model = $question;
    }
    
    public function index()
    {
        return response()->json(["dados" => $this->model->all()]);
    }

    public function actives()
    {
        return response()->json(["dados" => $this->model->where("active", "S")->get()]);
    }
    
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $question = $this->model->find($id)->description;
        $answers = $this->model->find($id)->answers;
        return response()->json(["question" => $question, "answers" => $answers]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
