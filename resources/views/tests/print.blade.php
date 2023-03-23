
<style>
    *{
        margin: 10px;
        font-family: cursive, Arial
    }
    body{
        font-family: cursive, Arial !important
    }
   /*  hr{
        background-color: #000;
        height: 1px
    } */
    strong{
        color: #006E3E
    }
</style>
<!-- <div style="border: 2px solid #000; width: 100%; height: 100%"> -->
<div>
    <div style="width: 100%; clear: both;">
        <div style="width: 25%; float: left">
            <img src="https://quizvicentino.com.br/images/logo.png" alt="Quiz Vicentino" style="width: 100%">
        </div>
        <div style="width: 75%; float: left; text-align: center">
            <p style="font-size: 1.5em; text-align: center"><strong>Teste {{ $testHeaderData->personalID ?? $testHeaderData->id }}</strong></p>
            <div style="width: 100%">
                <div style="width: 50%; float: left"><strong>Nome:</strong> {{ $testHeaderData->name }}</div>
                <div style="width: 50%; float: right"><strong>Cidade:</strong> {{ $testHeaderData->city }}/{{ $testHeaderData->state }}</div>
            </div>
            <div style="width: 100%; margin-top: 30px">
                <div style="width: 40%; float: left"><strong>Data/Hora:</strong> {{ date("d/m/Y H:i", strtotime($testHeaderData->created_at)) }}</div>
                <div style="width: 60%; float: right">
                    <div style="width: 40%; float: left; margin-top: 0"><strong>Pontuação:</strong> {{ $testHeaderData->points }}</div>
                    <div style="width: 60%; float: right; margin-top: 0"><strong>Tempo:</strong> {{ $testHeaderData->time }}</div>
                </div>
            </div>
        </div>
    </div>
    <div style="width: 100%; margin-top: 10px; clear: both">
        <hr style="border-top: 0.5px solid #006E3E; margin-top: 10px" />
        @foreach($testQuestions as $key => $question)
            <p style="margin-top: 20px">{{ ++$key }} - {{ $question["description"] }}</p>
            @foreach($question["answers"] as $answer)
                @if($answer["id"] == $question["userAnswerID"])
                    @if($question["correctAnsweID"] == $answer["id"])
                        <p style="color: green">* {{ $answer["description"] }}</p>
                    @else
                        <p style="color: red">* {{ $answer["description"] }}</p>
                    @endif
                @else
                    <p>* {{ $answer["description"] }}</p>
                @endif
            @endforeach
        @endforeach
    </div>
</div>