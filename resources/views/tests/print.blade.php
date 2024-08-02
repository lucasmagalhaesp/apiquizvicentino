
<style>
    *{
        font-family: cursive, Arial
    }
    body{
        font-family: cursive, Arial !important
    }
   /*  hr{
        background-color: #000;
        height: 1px
    } */
    .cabecalho{
        color: #DCE4E3
    }
    .perguntas{
        width: 100%;
        clear: both;
        background: #006E3E;
        color: #DCE4E3;
    }
</style>
<!-- <div style="border: 2px solid #000; width: 100%; height: 100%"> -->
<div style="margin-top: -20px">
    <div class="cabecalho" style="width: 100%; clear: both; background: #006E3E">
        <h2 style="text-align: center; padding: 10px 0; background: #DCE4E3; color: #000">Teste n° {{ $testHeaderData->personalID ?? $testHeaderData->id }}</h2>
        <table width="100%">
            <tr width="100%">
                <td width="30%" style="padding-left: 20px">
                    <img src="https://quizvicentino.com.br/images/logo.png" alt="logo_relatório" style="max-height: 100px">
                </td>
                <td width="20%"></td>
                <td width="50%" style="padding-left: 20px;">
                    <p><strong>Nome:</strong> {{ $testHeaderData->name }}</p>
                    <p><strong>Cidade:</strong> {{ $testHeaderData->city }}/{{ $testHeaderData->state }}</p>
                    <p><strong>Data/Hora:</strong> {{ date("d/m/Y H:i", strtotime($testHeaderData->created_at)) }}</p>
                    <p><strong>Pontuação:</strong> {{ $testHeaderData->points }}</p>
                    <p><strong>Tempo:</strong> {{ $testHeaderData->time }}</p>
                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 20px; margin-bottom: 0; text-align: center; background: #DCE4E3; color: #000">
        <h2 style="padding: 8px 0; margin: 0">Perguntas</h2>
    </div>
    <div class="perguntas">
        <!-- <hr style="border-top: 0.5px solid #006E3E; margin-top: 10px" /> -->
        <div style="padding: 5px 8px">
        @foreach($testQuestions as $key => $question)
            @if($key == 0)
                <p style="margin-top: 0; font-weight: bold">{{ ++$key }} - {{ $question["description"] }}</p>
            @else
                <p style="margin-top: 20px; font-weight: bold">{{ ++$key }} - {{ $question["description"] }}</p>
            @endif
            @foreach($question["answers"] as $answer)
                @if($answer["id"] == $question["userAnswerID"])
                    @if($question["correctAnsweID"] == $answer["id"])
                        <p><span style="background: #FFF; color: green; padding: 5px">* {{ $answer["description"] }}</span></p>
                    @else
                        <p><span style="background: #FFF; color: red; padding: 5px">* {{ $answer["description"] }}</span></p>
                    @endif
                @else
                    <p>* {{ $answer["description"] }}</p>
                @endif
            @endforeach
            @if($key < 5) <hr /> @endif
        @endforeach
        </div>
    </div>
</div>