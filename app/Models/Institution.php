<?php
namespace App\Models;

class Institution extends BaseModel
{
    protected $table = 'instituicoes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nome',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'logo',
        'status',
        'data_expiracao',
        'plano'
    ];
    
    /**
     * Retorna instituições ativas
     */
    public function ativos()
    {
        return $this->where('status = :status', ['status' => 'ativo']);
    }
    
    /**
     * Verifica se a instituição está ativa
     */
    public function estaAtiva($id)
    {
        $instituicao = $this->find($id);
        return $instituicao && $instituicao['status'] === 'ativo';
    }
    
    /**
     * Verifica se a instituição está dentro da data de expiração
     */
    public function dentroDaValidade($id)
    {
        $instituicao = $this->find($id);
        
        if (!$instituicao) {
            return false;
        }
        
        // Se não tiver data de expiração, consideramos válido
        if (empty($instituicao['data_expiracao'])) {
            return true;
        }
        
        $hoje = date('Y-m-d');
        return $instituicao['data_expiracao'] >= $hoje;
    }
    
    /**
     * Atualiza o status da instituição
     */
    public function atualizarStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}