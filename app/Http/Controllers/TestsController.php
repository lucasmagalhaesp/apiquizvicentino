<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;

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
        return response()->json(["dados" => $this->model->all()]);
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
            $question = $activesQuestions[$id]->description;
            
            $answers = $activesQuestions[$id]->answers->map(function ($a){
                return $a->description;
            });

            $questions[] = [
                "question" => $question,
                "answers"  => $answers
            ];
        }

        return response()->json(["questions" => $questions]);
    
    }

    private function chooseQuestions()
    {
    	$chosenNumbers = [];
    	$cont = 0;
    	$count = Question::where("active", "S")->count();
 
    	while($cont < $this->numQuestions){
            $num = rand(1,$count);
            if(!in_array($num, $chosenNumbers)){
                $chosenNumbers[$cont] = $num;
                $cont++;
            }
        }

    	return $chosenNumbers;
    }
}
