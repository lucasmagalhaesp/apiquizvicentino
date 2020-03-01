<div style="background: #008148; padding: 5px 0; text-align: center; color: #FFF; font-family: Verdana;"><h3>Quiz Vicentino - Recuperar Senha</h3></div>
<div style="background: #F0EEF3; padding: 5px 5px 15px">
<p>{{ explode(" ", $data["name"])[0] }}, clique no link abaixo para redefinir sua senha</p>
<a href={{ 'http://localhost:8080/reset-password/'.$token }} >Redefinir senha</a>
</div>