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
        $questions = $this->model->with("answers")->with("correctAnswer")->get();
        return response()->json(["success" => true, "questions" => $questions]);
    }

    public function actives()
    {
        return response()->json(["success" => true, "data" => $this->model->where("active", "S")->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->dados;
        $this->model->description = $data["description"];
        $this->model->active = $data["active"] ? "S" : "N";
        if (!is_null($data["expiration_date"])) $this->model->expiration_date = $data["expiration_date"];

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
        $question = $this->model->findOrFail($id);
        $answers = $question->answers;
        $correctAnswer = DB::table("correct_answers")->select("answer_id")->where("question_id", $question->id)->get()[0]->answer_id;
        
        return response()->json(["success" => true, "question" => $question, "answers" => $answers, "correctAnswer" => $correctAnswer]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->dados;
        $this->model = $this->model->find($id);
        $this->model->description = $data["description"];
        $this->model->active = $data["active"] ? "S" : "N";
        if (!is_null($data["expiration_date"])) $this->model->expiration_date = $data["expiration_date"];

        try{
            $this->model->save();
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao atualizar a pergunta: ".$e->getMessage()]);
        }

        $answersID = Answer::select("id")->where("question_id", $id)->get();

        $correctAnswer = $data["correctAnswer"];
        $correctAnswerID = 0;
        $cont = 0;
        foreach($data["answers"] as $key => $resp){
            $answer = Answer::find($answersID[$cont]->id);
            $answer->description = $resp;
            try{
                $answer->save();
            }catch(\Exception $e){
                return response()->json(["success" => false, "msg" => "Erro ao atualizar resposta $key: ".$e->getMessage()]);
            }
            
            if ($correctAnswer == $key) $correctAnswerID = $answer->id;
            $cont++;
        }

        if ($correctAnswerID == 0) 
            return response()->json(["success" => false, "msg" => "Resposta correta não informada"]);

        try{
            DB::table("correct_answers")->where("question_id", $id)->update(["answer_id" => $correctAnswerID]);
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao atualizar resposta correta: ".$e->getMessage()]);
        }

        return response()->json(["success" => true]);
    }

    public function destroy($id)
    {
        try{
            $this->model->find($id)->delete();
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao excluir pergunta: ".$e->getMessage()]);
        }

        return response()->json(["success" => true]);
    }
    
}
