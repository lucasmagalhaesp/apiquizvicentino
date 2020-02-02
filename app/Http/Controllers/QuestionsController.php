<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use DB;

class QuestionsController extends Controller
{
    private $model;

    public function __construct(Question $question)
    {
        $this->model = $question;
    }
    
    public function index()
    {
        return response()->json(["success" => true, "questions" => $this->model->all()]);
    }

    public function actives()
    {
        return response()->json(["success" => true, "data" => $this->model->where("active", "S")->get()]);
    }
    
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->dados;
        $this->model->description = $data["description"];
        $this->model->active = $data["active"] ? "S" : "N";
        $this->model->expiration_date = $data["expiration_date"];

        try{
            $this->model->save();
            $questionID = $this->model->id;
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao cadastrar a pergunta: ".$e->getMessage()]);
        }

        $correctAnswer = $data["correctAnswer"];
        $correctAnswerID = 0;
        foreach($data["answers"] as $key => $resp){
            $answer = new Answer;
            $answer->question_id = $questionID;
            $answer->description = $resp;
            try{
                $answer->save();
            }catch(\Exception $e){
                return response()->json(["success" => false, "msg" => "Erro ao cadastrar resposta $key: ".$e->getMessage()]);
            }
            
            if ($correctAnswer == $key) $correctAnswerID = $answer->id;
        }

        if ($correctAnswerID == 0) 
            return response()->json(["success" => false, "msg" => "Resposta correta não informada"]);

        try{
            DB::table("correct_answers")->insert(
                ["question_id" => $questionID, "answer_id" => $correctAnswerID]
            );
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao cadastrar resposta correta: ".$e->getMessage()]);
        }

        return response()->json(["success" => true]);
    }

    public function show($id)
    {
        $question = $this->model->find($id)->description;
        $answers = $this->model->find($id)->answers;
        return response()->json(["success" => true, "data" => ["question" => $question, "answers" => $answers]]);
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
