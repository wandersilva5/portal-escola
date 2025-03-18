# Portal Escolar - Sistema SaaS MVC

Sistema de gestão escolar multi-instituição desenvolvido em PHP puro com arquitetura MVC.

## Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache, Nginx, etc.)
- Composer (opcional, para gerenciamento de dependências)

## Estrutura do Projeto

```
portal-escola/
├── app/
│   ├── Controllers/     # Controladores da aplicação
│   ├── Core/            # Classes de núcleo (Router, Database, etc.)
│   ├── Helpers/         # Classes auxiliares
│   ├── Models/          # Modelos para acesso aos dados
│   └── Views/           # Camada de visualização
├── config/              # Arquivos de configuração
├── public/              # Arquivos públicos (index.php, assets, etc.)
│   ├── assets/          # CSS, JavaScript, imagens
│   └── uploads/         # Uploads de arquivos
└── vendor/              # Dependências (se usando Composer)
```

## Instalação

### 1. Clone o repositório

```bash
git clone https://seu-repositorio/portal-escola.git
cd portal-escola
```

### 2. Configure o banco de dados

Crie um banco de dados MySQL e importe o arquivo `database.sql` para criar a estrutura inicial:

```sql
CREATE DATABASE portal_escolar;
USE portal_escolar;
```

### 3. Configure o arquivo .env

Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:

```bash
cp .env.example .env
```

Edite o arquivo `.env` com as configurações do seu ambiente.

### 4. Configuração do servidor web

#### Apache

Certifique-se de que o mod_rewrite está ativado e que o .htaccess está funcionando corretamente.

#### Nginx

Configure o seu site para apontar para o diretório `public/` e adicione regras de reescrita para o `index.php`.

### 5. Instalação com Composer (opcional, mas recomendado)

Se você preferir usar o Composer para gerenciar dependências:

```bash
composer install
```

## Estrutura do Banco de Dados

### Tabelas principais:

- `instituicoes` - Cadastro de instituições de ensino
- `usuarios` - Usuários do sistema (administradores, professores, alunos)

## Permissões de Usuário

O sistema possui os seguintes tipos de usuário:

- `admin`: Administrador do sistema (acesso total)
- `instituicao_admin`: Administrador da instituição
- `professor`: Professor (acesso restrito a funcionalidades acadêmicas)
- `aluno`: Aluno (acesso a informações pessoais e acadêmicas)
- `responsavel`: Responsável pelo aluno (acesso às informações do aluno vinculado)

## Configuração com Composer

Se você quiser utilizar o Composer para gerenciar as dependências do projeto, siga os passos abaixo:

1. Instale o Composer (https://getcomposer.org/download/)

2. Crie um arquivo `composer.json` na raiz do projeto:

```json
{
    "name": "seu-usuario/portal-escola",
    "description": "Sistema de gestão escolar multi-instituição",
    "type": "project",
    "require": {
        "php": ">=8.0",
        "vlucas/phpdotenv": "^5.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
}
```

3. Execute o comando de instalação:

```bash
composer install
```

4. Modifique o arquivo `public/index.php` para usar o autoloader do Composer:

```php
// Substituir o autoloader atual pelo do Composer
require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

// Carregar variáveis de ambiente (com phpdotenv)
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();
```

## Solução de Problemas

### O sistema está exibindo erro "Failed to open stream: No such file or directory" para o autoloader

Se você não está usando o Composer, o sistema já possui um autoloader simples implementado. Certifique-se de que:

1. A estrutura de diretórios está correta
2. As namespaces das classes correspondem ao caminho dos arquivos
3. Os arquivos têm permissão de leitura adequada

### Redirecionamentos não estão funcionando corretamente

Verifique se:

1. O mod_rewrite está ativado (Apache)
2. O .htaccess está configurado corretamente
3. As permissões do diretório permitem a execução do .htaccess

## Desenvolvimento

### Convenções de código

- Use namespaces PSR-4
- Nomes de classes em UpperCamelCase
- Nomes de métodos em lowerCamelCase
- Indentação com 4 espaços

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes.

# Diretório de Imagens

Este diretório armazena as imagens do sistema, como ícones, logos e placeholders.

## Arquivos necessários:

- `user-placeholder.jpg` - Imagem de placeholder para usuários sem foto
- `logo.png` - Logo do sistema
- `favicon.ico` - Ícone do sistema

Certifique-se de que estes arquivos estejam presentes para o funcionamento correto do sistema.