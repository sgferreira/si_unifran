<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/src/bootstrap.php';
use App\Core\Database;
use App\Core\Validation;

function ask(string $label):string {echo $label;return trim((string)fgets(STDIN));}
$name=ask('Nome do administrador: ');
$email=strtolower(ask('E-mail do administrador: '));
$password=ask('Senha (mínimo de 8 caracteres): ');
if(mb_strlen($name)<3 || !Validation::email($email) || strlen($password)<8 || strlen($password)>72){
    fwrite(STDERR,"Dados inválidos.\n");exit(1);
}
$db=Database::connection();
$stmt=$db->prepare("INSERT INTO usuarios (nome,email,senha_hash,perfil) VALUES (?,?,?,'ADMIN')");
try{
    $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
    echo "Administrador criado.\n";
}catch(PDOException $e){
    if($e->getCode()==='23000') fwrite(STDERR,"E-mail já cadastrado.\n");
    else fwrite(STDERR,"Falha no cadastro. Verifique o banco.\n");
    exit(1);
}
