<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Test;
use App\Models\Question;
use App\User;
use DB;

class TestsController extends Controller
{
    private $model;
    private $numQuestions = 5;

    public function __construct(Test $test)
    {
        $this->model = $test;
    }

    public function index()
    {
        return response()->json(["success" => true, "data" => $this->model->all()]);
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
        //
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

    public function createTest()
    {
        $idActiveQuestions = $this->chooseQuestions();
        $activesQuestions = Question::where("active", "S")->get();
        $questions = [];

        foreach($idActiveQuestions as $id){
            $idQuestion = $activesQuestions[$id]->id;
            $question = $activesQuestions[$id]->description;
            $answers = $activesQuestions[$id]->answers->map(function ($a){
                return ["id" => $a->id, "description" => $a->description];
            });

            $questions[] = ["question" =>   ["id" => $idQuestion, "description" => $question], 
                            "answers" => $answers];
        }

        return response()->json(["success" => true, "data" => $questions]);
    }

    private function chooseQuestions()
    {
    	$chosenNumbers = [];
    	$cont = 0;
    	$count = Question::where("active", "S")->count();
 
    	while($cont < $this->numQuestions){
            $num = rand(0,$count - 1);
            if(!in_array($num, $chosenNumbers)){
                $chosenNumbers[$cont] = $num;
                $cont++;
            }
        }

    	return $chosenNumbers;
    }

    public function selectTestQuestion(Request $request){
        $idQuestions = $request->idQuestions;
        
        $activesQuestions = Question::where("active", "S")->get();
        $count = Question::where("active", "S")->count();

        $allowedId = false;
        $num = -1;
        while($allowedId == false){
            $num = rand(0, $count - 1);
            if(!in_array($num, $idQuestions)){
                $allowedId = true;
            }
        }

        $idQuestion = $activesQuestions[$num]->id;
        $question = $activesQuestions[$num]->description;
        $answers = $activesQuestions[$num]->answers->map(function ($a){
            return ["id" => $a->id, "description" => $a->description];
        });

        $question = [   
                        "question" => [ "id" => $idQuestion, "description" => $question ], 
                        "answers" => $answers
                    ];
        
        return response()->json(["success" => true, "data" => $question, "indexQuestion" => $num]);
    }

    public function correctAnswer(Request $request)
    {
        $idQuestion = $request->idQuestion;
        $idCorrectAnswer = DB::table("correct_answers")->select("answer_id")->where("question_id", $idQuestion)->first()->answer_id;
        
        return response()->json(["success" => true, "idCorrectAnswer" => $idCorrectAnswer]);
    }

    public function result()
    {

    }

    private function resultText($numHits)
    {
        switch($numHits){
            case 0:
                $resultText = "<strong>Infelizmente você não acertou nenhuma pergunta!!!</strong><br /> Faça um novo teste e melhore seu resultado.";
                break;
            case 1:
                $resultText = "<strong>Você não foi bem!!!</strong><br /> Faça um novo teste e melhore seu resultado.";
                break;
            case 2:
                $resultText = "<strong>Você pode melhorar esse resultado!!!</strong><br /> Faça um novo teste.";
                break;
            case 3:
                $resultText = "<strong>PARABÉNS!!!</strong> Você foi bem no teste!!!<br /> Mas ainda pode melhorar. Faça um novo teste.";
                break;
            case 4:
                $resultText = "<strong>EXCELENTE!!!</strong> VOCÊ QUASE ALCANÇOU O OBJETIVO!!!<br /> Faça um novo teste e tente acertar todas.";
                break;
            case 5:
                $resultText = "<strong>ESPETACULAR!!!</strong> VOCÊ ACERTOU TODAS AS PERGUNTAS!!!<br /> TENTE REPETIR ESSE FEITO!!!";
                break;
            default:
                $resultText = "Erro ao retornar o resultado!";
        }
        
        return $resultText;
    }

    public function ranking()
    {
        //pega o id de cada usuário
        $users = User::select("id")->orderBy("id")->get();
        $idUsers = $users->map(function ($user){
            return $user->id;
        });

        //pega o melhor teste de cada usuário
        $ranking = [];
        foreach($idUsers as $id){
            $userTests = $this->model->where("user_id", $id)->orderBy("points", "desc")->orderBy("time")->first();
            if (!is_null($userTests)){
                $ranking[] = [
                    "id" => $userTests->user_id,
                    "points" => $userTests->points,
                    "time" => $userTests->time,
                    "created_at" => $userTests->created_at
                ];
            }
        }

        //ordena os testes de acordo com a maior pontuação e o menor tempo
        usort($ranking, function ($a, $b){
            if ($a["points"] == $b["points"]){
                return $a["time"] > $b["time"];
            }
            return $a["points"] < $b["points"];
        });

        return response()->json(["success" => true, "data" => $ranking]);
    }

    public function allUserTests(Request $request)
    {
        $userId = $request->userId;
        $tests = User::find($userId)->tests;
        
        return response()->json(["success" => true, "data" => $tests]);
    }
}
