<?php 
class usuario{
    private $nomeUsu;
    private $sobrenomeUsu;
    private $dataNascUsu;
    private $cpfUsu;
    private $enderecoUsu;
    private $emailUsu;
    private $perfilUsu;
    private $statusUsu;

    public function setNomeUsu($nomeUsu){
        $this->nomeUsu = $nomeUsu;
    }
    public function getNomeUsu(){
        return $this->nomeUsu;
    }

    public function setSobrenomeUsu($sobrenomeUsu){
        $this->sobrenomeUsu = $sobrenomeUsu;
    }
    public function getSobrenomeUsu(){
        return $this->sobrenomeUsu;
    }
    public function setDataNascUsu($dataNascUsu){
        $this->dataNascUsu = $dataNascUsu;
    }
    public function getDataNascUsu(){
        return $this->dataNascUsu;
    }
    public function setCpfUsu($cpfUsu){
        $this->cpfUsu = $cpfUsu;
    }
    public function getCpfUsu(){
        return $this->cpfUsu;
    }
    public function setEnderecoUsuu($enderecoUsu){
        $this->enderecoUsu = $enderecoUsu;
    }
    public function getEnderecoUsu(){
        return $this->enderecoUsu;
    }
    public function setEmailUsu($emailUsu){
        $this->emailUsu = $emailUsu;
    }
    public function getEmailUsu(){
        return $this->emailUsu;
    }
    public function setPerfilUsu($perfilUsu){
        $this->perfilUsu = $perfilUsu;
    }
    public function getPerfilUsu(){
        return $this->perfilUsu;
    }
    public function setStatusUsu($statusUsu){
        $this->statusUsu = $statusUsu;
    }
    public function getStatusUsu(){
        return $this->statusUsu;
    }

}

?>