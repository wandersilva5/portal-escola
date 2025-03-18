<?php
namespace App\Models;

class Institution extends BaseModel
{
    protected $table = 'institutions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'logo',
        'status',
        'expiration_date',
        'plan'
    ];
    
    /**
     * Retorna instituições ativas
     */
    public function actives()
    {
        return $this->where('status = :status', ['status' => 'active']);
    }
    
    /**
     * Verifica se a instituição está ativa
     */
    public function isActive($id)
    {
        $institution = $this->find($id);
        return $institution && $institution['status'] === 'active';
    }
    
    /**
     * Verifica se a instituição está dentro da data de expiração
     */
    public function withinValidity($id)
    {
        $institution = $this->find($id);
        
        if (!$institution) {
            return false;
        }
        
        // Se não tiver data de expiração, consideramos válido
        if (empty($institution['expiration_date'])) {
            return true;
        }
        
        $hoje = date('Y-m-d');
        return $institution['expiration_date'] >= $hoje;
    }
    
    /**
     * Atualiza o status da instituição
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}