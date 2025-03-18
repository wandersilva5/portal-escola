<?php
namespace App\Models;

class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'status',
        'institution_id',
        'ultimo_acesso',
        'phone',
        'photo',
        'created_at',
        'updated_at',
    ];
    
    
    public function authenticate($email, $password)
    {
        $usuario = $this->findWhere('email = :email', ['email' => $email]);
        
        if (!$usuario) {
            return false;
        }
        
        // Verifica password
        if (!password_verify($password, $usuario['password'])) {
            return false;
        }
        
        // Verifica se usuário está ativo
        if ($usuario['status'] !== 'ativo') {
            return false;
        }
        
        // Atualiza data do último acesso
        $this->update($usuario['id'], [
            'last_access' => date('Y-m-d H:i:s')
        ]);
        
        return $usuario;
    }
    
    /**
     * Cria um novo usuário
     */
    public function createUser($data)
    {
        // Hash na password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }
    
    /**
     * Atualiza um usuário
     */
    public function updateUser($id, $data, $instituicaoId = null)
    {
        // Hash na password se estiver sendo alterada
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Remove password vazia para não atualizar
            unset($data['password']);
        }
        
        return $this->update($id, $data, $instituicaoId);
    }
    
    /**
     * Lista usuários por instituição
     */
    public function getPorInstituicao($instituicaoId)
    {
        return $this->where('institution_id = :institution_id', ['institution_id' => $instituicaoId]);
    }
    
    /**
     * Lista usuários por tipo
     */
    public function getType($type, $instituicaoId = null)
    {
        return $this->where('type = :type', ['type' => $type], $instituicaoId);
    }
}