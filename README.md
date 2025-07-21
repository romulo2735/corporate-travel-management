## 🧳 Travel Request API

API RESTful para gerenciamento de pedidos de viagens corporativas, construída com **Laravel**.

## 🚀 Funcionalidades

- Solicitação de viagens por colaboradores
- Listagem de pedidos com filtros por status, destino e data
- Aprovação e rejeição de viagens por gestores
- Cancelamento de viagens aprovadas
- Autenticação com base no usuário logado
- Autorização por políticas (Gates)

## 📦 Tecnologias Utilizadas

- [Laravel 12+](https://laravel.com/)
- [PHP 8.2+](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Docker](https://www.docker.com/)
- PHPUnit (testes automatizados)

## 📁 Estruturaapp/

```
app/
├── DTOs/ # Transferência de dados
├── Events/ # Eventos do sistema
├── Http/
│ ├── Controllers/ # Controllers da API
│ ├── Requests/ # Validações
│ └── Resources/ # Transformações de resposta
├── Models/ # Modelos Eloquent
├── Policies/ # Autorização
├── Repositories/ # Acesso aos dados
└── Services/ # Lógica de negócio
```

## ⚙️ Instalação

```bash
git clone https://github.com/seu-usuario/travel-api.git
cd travel-api

cp .env.example .env
composer install
php artisan key:generate

# Configure seu banco no .env e rode as migrations
php artisan migrate --seed
```

## 🧪 Testes

Rodar todos os testes:

```bash
php artisan test
```

## 🔐 Autenticação e Autorização

Utiliza autenticação baseada no usuário logado via Auth::user()

Regras de autorização são controladas por Policies e Gates para garantir que usuários não aprovem ou cancelem suas
próprias viagens.

## 🔁 Fluxo de Aprovação

1. Colaborador solicita viagem (POST /api/travels)

2. Gestor lista pedidos pendentes (GET /api/travels?status=requested)

3. Gestor aprova/rejeita (PATCH /api/travels/{id}/status)

4. Viagem aprovada pode ser cancelada por outro gestor (PATCH /api/travels/{id}/cancel)

## 📬 Endpoints


| Método | Rota                     | Descrição                   |
| ------ | ------------------------ | --------------------------- |
| GET    | /api/travels             | Lista viagens (com filtros) |
| GET    | /api/travels/{id}        | Detalhes da viagem          |
| POST   | /api/travels             | Criar novo pedido de viagem |
| PATCH  | /api/travels/{id}/status | Atualiza o status da viagem |
| PATCH  | /api/travels/{id}/cancel | Cancela uma viagem aprovada |


## Postman Collection
[Corporate Travel Management.postman_collection.json](Corporate%20Travel%20Management.postman_collection.json)
