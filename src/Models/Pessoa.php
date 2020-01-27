<?php

namespace JbGlobal\Models;

use JbAuthJwt\Auth\PessoaAuth;

class Pessoa extends PessoaAuth
{
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'alterado_em';
    const DELETED_AT = 'deletado_em';

    protected $fillable = [
        'id','nome','email','email_verificado_em','ativo'
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }

}
