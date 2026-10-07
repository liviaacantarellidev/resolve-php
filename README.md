# RESOLVE.

Sistema web de gerenciamento de chamados desenvolvido em PHP.

O projeto foi criado com o objetivo de praticar desenvolvimento backend, manipulação de banco de dados e construção de operações CRUD utilizando PHP e SQLite.

## Funcionalidades

- Cadastro de chamados
- Listagem de chamados
- Edição de chamados
- Exclusão de chamados
- Pesquisa por cliente ou assunto
- Filtro por status
- Filtro por prioridade
- Dashboard com indicadores
- Validação de dados
- Interface responsiva

## Tecnologias

- PHP 8
- SQLite
- PDO
- SQL
- HTML5
- CSS3
- XAMPP / Apache

## Banco de dados

O sistema utiliza SQLite para persistência dos dados.

A tabela `tickets` possui os seguintes campos:

- `id`
- `cliente`
- `assunto`
- `prioridade`
- `status`
- `criado_em`

A comunicação entre PHP e SQLite é realizada utilizando PDO e prepared statements.

## Segurança

O projeto utiliza:

- Prepared statements
- Validação de parâmetros
- Escape de dados exibidos no HTML
- Requisições POST para exclusão

## Como executar

1. Instale o XAMPP.
2. Coloque o projeto dentro da pasta `htdocs`.
3. Inicie o Apache no XAMPP.
4. Acesse no navegador:

http://localhost/resolve-php/

O banco SQLite e a tabela necessária são criados automaticamente na primeira execução.

## Estrutura

```text
resolve-php/
├── assets/
│   └── style.css
├── config/
│   └── database.php
├── database/
├── criar.php
├── editar.php
├── excluir.php
└── index.php