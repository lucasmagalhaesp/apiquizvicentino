<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Mail;

class ContactController extends Controller
{
    public function send(ContactRequest $request){
        Mail::send("contact.email", [ "data" => $request->dados ], function ($message) {
            $message->from("contato@quizvicentino.com.br")
                    ->to("contato@quizvicentino.com.br", "Quiz Vicentino")
                    ->subject("Contato do site");
        });

        return response()->json(["success" => true]);
    }
}
