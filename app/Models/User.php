<?php
namespace App\Models;

class User extends BaseModel
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo',
        'status',
        'instituicao_id',
        'ultimo_acesso',
        'telefone',
        'foto'
    ];
    
    
    public function authenticate($email, $senha)
    {
        $usuario = $this->findWhere('email = :email', ['email' => $email]);
        
        if (!$usuario) {
            return false;
        }
        
        // Verifica senha
        if (!password_verify($senha, $usuario['senha'])) {
            return false;
        }
        
        // Verifica se usuário está ativo
        if ($usuario['status'] !== 'ativo') {
            return false;
        }
        
        // Atualiza data do último acesso
        $this->update($usuario['id'], [
            'ultimo_acesso' => date('Y-m-d H:i:s')
        ]);
        
        return $usuario;
    }
    
    /**
     * Cria um novo usuário
     */
    public function createUser($data)
    {
        // Hash na senha
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }
    
    /**
     * Atualiza um usuário
     */
    public function updateUser($id, $data, $instituicaoId = null)
    {
        // Hash na senha se estiver sendo alterada
        if (isset($data['senha']) && !empty($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        } else {
            // Remove senha vazia para não atualizar
            unset($data['senha']);
        }
        
        return $this->update($id, $data, $instituicaoId);
    }
    
    /**
     * Lista usuários por instituição
     */
    public function getPorInstituicao($instituicaoId)
    {
        return $this->where('instituicao_id = :instituicao_id', ['instituicao_id' => $instituicaoId]);
    }
    
    /**
     * Lista usuários por tipo
     */
    public function getPorTipo($tipo, $instituicaoId = null)
    {
        return $this->where('tipo = :tipo', ['tipo' => $tipo], $instituicaoId);
    }
}