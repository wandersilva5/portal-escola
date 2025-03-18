<?php
namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $instituicaoKey = 'instituicao_id';
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Busca todos os registros (filtrados por instituição)
     */
    public function all($instituicaoId = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " WHERE {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca um registro pelo ID
     */
    public function find($id, $instituicaoId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $params = ['id' => $id];
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " AND {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Cria um novo registro
     */
    public function create(array $data)
    {
        // Filtra apenas campos permitidos
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($filteredData)) {
            return false;
        }
        
        $fields = array_keys($filteredData);
        $placeholders = array_map(function($field) {
            return ":$field";
        }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Atualiza um registro existente
     */
    public function update($id, array $data, $instituicaoId = null)
    {
        // Filtra apenas campos permitidos
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($filteredData)) {
            return false;
        }
        
        $fields = [];
        foreach ($filteredData as $key => $value) {
            $fields[] = "$key = :$key";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $params = array_merge($filteredData, ['id' => $id]);
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " AND {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Remove um registro
     */
    public function delete($id, $instituicaoId = null)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $params = ['id' => $id];
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " AND {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Busca registros com condições personalizadas
     */
    public function where($conditions, $params = [], $instituicaoId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " AND {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca um único registro com condições personalizadas
     */
    public function findWhere($conditions, $params = [], $instituicaoId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        
        // Filtrar por instituição caso seja um modelo multi-tenant
        if ($instituicaoId && in_array($this->instituicaoKey, $this->fillable)) {
            $sql .= " AND {$this->instituicaoKey} = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}