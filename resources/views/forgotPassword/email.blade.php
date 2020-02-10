<h3>Recuperar Senha</h3>
<p>{{ explode(" ", $data["name"])[0] }}, clique no link abaixo para redefinir sua senha</p>
<a href={{ 'http://localhost:8080/reset-password/'.$token }} >Redefinir senha</a>