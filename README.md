# Laravel 12 com Docker - Guia de Execução

Este README explica como configurar e executar uma aplicação Laravel 12 usando Docker, incluindo o banco de dados MySQL, os assets front-end, logs e testes unitários.

---

## 📋 Pré-requisitos

- **Docker** instalado ([Download Docker](https://www.docker.com/))
- **Docker Compose** (geralmente incluso no Docker Desktop)
- **Git** (opcional, para clonar o projeto)

---

## 🚀 Configuração Inicial

### 1. Clone o projeto (se aplicável)

```bash
git clone [seu-repositorio.git]
cd [nome-do-projeto]
```

### 2. Configure as variáveis de ambiente

Crie/edite o arquivo `.env` na raiz do projeto com as configurações do `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql         # Nome do serviço no docker-compose.yml
DB_PORT=3306
DB_DATABASE=db_dev    # Deve corresponder ao MYSQL_DATABASE no docker-compose
DB_USERNAME=root      # Deve corresponder ao MYSQL_ROOT_USER
DB_PASSWORD=root      # Deve corresponder ao MYSQL_ROOT_PASSWORD
```

> ⚠️ **Atenção**:
> - `DB_HOST` deve ser `mysql` (nome do serviço no Docker Compose).
> - As credenciais do MySQL devem corresponder às definidas no `docker-compose.yml`.

---

## 🐳 Executando com Docker

### 1. Construa e inicie os containers

```bash
docker-compose up -d --build
```

Isso criará e iniciará:

- Um container Laravel (PHP 8.2 + NPM)
- Um container MySQL 8.0

### 2. Execute as migrações

Após os containers estarem rodando, execute:

```bash
docker exec -it laravel_app php artisan migrate
```

💡 **Dica**: Se ocorrer um erro, verifique se o MySQL já está totalmente inicializado (pode levar alguns segundos).

### 3. Acesse a aplicação

- **Laravel (PHP Artisan Serve)**: [http://localhost:8000](http://localhost:8000)
- **Vite (Hot Reload)**: [http://localhost:5173](http://localhost:5173) (se estiver usando `npm run dev`)

---

## 🛠️ Logs

Os logs da aplicação Laravel podem ser acessados diretamente no container. Para visualizar os logs:

```bash
docker exec -it laravel_app tail -f storage/logs/laravel.log
```

💡 **Dica**: Use `Ctrl+C` para sair do modo de visualização contínua.

---

## 🧪 Testes Unitários

### Executando os testes

Para rodar os testes unitários da aplicação, utilize o seguinte comando:

```bash
docker exec -it laravel_app php artisan test
```

💡 **Dica**: Certifique-se de que as migrações e o ambiente de teste estão configurados corretamente antes de executar os testes.

---

## 🔧 Comandos Úteis

| Comando                                      | Descrição                                      |
|----------------------------------------------|------------------------------------------------|
| `docker-compose up -d`                       | Inicia os containers em segundo plano         |
| `docker-compose down`                        | Para e remove os containers                   |
| `docker-compose logs`                        | Mostra os logs dos serviços                   |
| `docker exec -it laravel_app bash`           | Acessa o terminal do container Laravel        |
| `docker exec -it laravel_app php artisan [comando]` | Executa comandos Artisan (ex: `migrate`, `make:controller`) |
| `docker exec -it laravel_app npm run dev`    | Compila os assets front-end (Vite)            |
| `docker exec -it laravel_app tail -f storage/logs/laravel.log` | Visualiza os logs da aplicação Laravel        |
| `docker exec -it laravel_app php artisan test` | Executa os testes unitários                  |

--- 
