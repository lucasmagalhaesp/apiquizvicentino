<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Test;
use App\Models\Question;
use App\User;
use DB;
use Auth;
use PDF;

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
        $this->middleware("apitoken");
        try{
            $tests = DB::table("tests")
                        ->join("users", "users.id", "tests.user_id")
                        ->select("tests.*", "users.name")
                        ->orderBy("tests.id", "desc")
                        ->get();
        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Erro ao buscar todos os testes {$e->getMessage()}"]);
        }
        
        return response()->json(["success" => true, "tests" => $tests]);
    }

    public function myTests()
    {
        $this->middleware("apitoken");
        if (is_null(auth()->user())) return response()->json(["success" => false]);
        $tests = $this->model->where("user_id", auth()->user()->id)->orderBy("id", "desc")->get();

        //Adicionando um "id personalizado" para cada teste do usuário iniciando do número 1
        $numTests = count($tests);
        foreach($tests as $key => $test){
            $test->personalID = $numTests - $key;
        }
        return response()->json(["success" => true, "tests" => $tests]);
    }
    
    public function store(Request $request)
    {
        $this->middleware("apitoken");
        $this->model->user_id = auth()->user()->id;
        $this->model->time = $request->time;
        $this->model->points = $request->hits;
        $questionsID = $request->questionsIds;
        $answersIds = $request->answersIds;
        
        try{
            $this->model->save();
        }catch(\Exception $exception){
            return response()->json(["success" => false, "msg" => "Erro ao salvar teste: ".$exception->getMessage()]);
        }

        if (!$this->saveTestAnswers($this->model->id, $questionsID, $answersIds))
            response()->json(["success" => false, "msg" => "Erro ao salvar as respostas do teste"]);

        return response()->json(["success" => true, "data" => $this->model]);
    }

    public function saveTestAnswers($testID, $questionsID, $answersIds)
    {
        $dados = [];
        foreach($questionsID as $key => $question){
            array_push($dados, [
                                "test_id"       => $testID,
                                "question_id"   => $question,
                                "answer_id"     => $answersIds[$key]
                                ]);
        }

        try{
            DB::table("test_answers")->insert($dados);
        }catch(\Exception $exception){
            return false;
        }

        return true;
    }

    public function createTest()
    {
        $idActiveQuestions = $this->chooseQuestions();
        $activesQuestions = Question::where("active", "S")->where("expiration_date", ">", date("Y-m-d"))->get();
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
    	//$count = Question::where("active", "S")->count();
    	$count = Question::count();
 
    	while($cont < $this->numQuestions){
            $num = rand(1,$count);
            $question = Question::find($num);
            $isActive = $question->active == "S" && $question->expiration_date > date("Y-m-d");
            if($isActive && !in_array($num, $chosenNumbers)){
                $chosenNumbers[$cont] = $num;
                $cont++;
            }
        }

    	return $chosenNumbers;
    }

    public function selectTestQuestion(Request $request){
        $idQuestions = $request->idQuestions;
        
        $activesQuestions = Question::where("active", "S")->where("expiration_date", ">", date("Y-m-d"))->get();
        $activesIDs = array_column($activesQuestions->toArray(), "id");

        $allowedId = false;
        while(!$allowedId){
            $index = rand(0, count($activesIDs) - 1);
            $idQuestionSel = $activesIDs[$index];
            if (!in_array($idQuestionSel, $idQuestions)){
                $allowedId = true;
            }
        }

        $idQuestion = $idQuestionSel;
        $question = $activesQuestions[$index]->description;
        $answers = $activesQuestions[$index]->answers->map(function ($a){
            return ["id" => $a->id, "description" => $a->description];
        });

        $question = [   
                        "question" => [ "id" => $idQuestion, "description" => $question ], 
                        "answers" => $answers
                    ];
        
        return response()->json(["success" => true, "data" => $question, "indexQuestion" => $idQuestionSel]);
    }

    public function correctAnswer(Request $request)
    {
        $idQuestion = $request->idQuestion;
        $idCorrectAnswer = DB::table("correct_answers")->select("answer_id")->where("question_id", $idQuestion)->first()->answer_id;
        
        return response()->json(["success" => true, "idCorrectAnswer" => $idCorrectAnswer]);
    }

    public function resultText(Request $request)
    {
        switch($request->numHits){
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
        
        return response()->json(["success" => true, "data" => $resultText]);
    }

    public function ranking()
    {
        $this->middleware("apitoken");
        if (is_null(auth()->user())) return response()->json(["success" => false]);
        
        //pega o id de cada usuário
        $users = User::select("id", "name", "email", "city", "state")->orderBy("id")->get();
        /* $idUsers = $users->map(function ($user){
            return $user->id;
        });

        dd($idUsers); */
        //pega o melhor teste de cada usuário
        $ranking = [];
        foreach($users as $user){
            $userTests = $this->model->where("user_id", $user->id)->orderBy("points", "desc")->orderBy("time")->first();
            if (!is_null($userTests)){
                $ranking[] = [
                    "id" => $userTests->user_id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "city" => $user->city."/".$user->state,
                    "points" => $userTests->points,
                    "time" => $userTests->time,
                    "created_at" => date("Y-m-d H:i:s", strtotime($userTests->created_at))
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

        return response()->json(["success" => true, "ranking" => $ranking]);
    }

    public function allUserTests(Request $request)
    {
        $this->middleware("apitoken");
        $userId = $request->userId;
        $tests = User::find($userId)->tests;
        
        return response()->json(["success" => true, "data" => $tests]);
    }

    public function print(Request $request)
    {
        $id = $request->id ?? null;
        if (is_null($id)) return response()->json(["success" => false, "msg" => "Erro ao imprimir relatório do teste"]);

        try{
            $testHeaderData = DB::table("tests")
                                ->select("tests.*", "users.name", "users.city", "users.state")
                                ->join("users", "users.id", "tests.user_id")
                                ->where("tests.id", $id)
                                ->first();

            //descobrindo o "ID pessoal" desse teste
            $userTests = DB::table("tests")->where("user_id", auth()->user()->id)->get();
            foreach($userTests as $key => $test){
                if ($test->id == $id) $testHeaderData->personalID = ++$key;
            }
            $seconds = $testHeaderData->time;
            $minutes = floor($seconds / 60);
            $seconds = $seconds - ($minutes * 60);
            $testHeaderData->time = (strlen($minutes) == 2 ? $minutes : "0".$minutes).":".(strlen($seconds) == 2 ? $seconds : "0".$seconds)." min";

            $testAnswerData = DB::table("test_answers")
                                ->select("question_id", "answer_id")
                                ->where("test_id", $id)
                                ->orderBy("id")
                                ->get();

            if (count($testAnswerData) == 0) return response()->json(["success" => false, "msg" => "Esse teste não possui relatório"], 500);

            $testQuestions = [];
            foreach($testAnswerData as $data){
                $question["userAnswerID"] = $data->answer_id;
                $question["description"] = DB::table("questions")
                                            ->where("id", $data->question_id)
                                            ->value("description");
                $answersID = DB::table("answers")
                                ->where("question_id", $data->question_id)
                                ->get();
                $question["answers"] = [];
                foreach($answersID as $answer){
                    array_push($question["answers"], [
                        "id" => $answer->id,
                        "description" => $answer->description
                    ]);
                }

                $question["correctAnsweID"] = DB::table("correct_answers")
                                            ->where("question_id", $data->question_id)
                                            ->value("answer_id");

                $testQuestions[] = $question;
            }

            $pdf = PDF::loadView("tests.print", compact("testHeaderData", "testQuestions"));
            return $pdf->stream();
            
        }catch(\Exception $e){
            return response()->json(["sucess" => false, "msg" => "Erro ao buscar os dados do teste. {$e->getMessage()}"]);
        }
        
    }
}
