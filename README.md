# Projeto API REST em Laravel

Bem-vindo ao **Projeto API REST em Laravel**! Este repositório contém a implementação de uma API desenvolvida em Laravel para gerenciamento de servidores, unidades e lotações.

## 📌 Tecnologias Utilizadas

- **Laravel** - Framework PHP para desenvolvimento backend
- **PostgreSQL** - Banco de dados relacional
- **MinIO** - Armazenamento de objetos compatível com S3
- **Docker** - Contêineres para ambiente isolado
- **Postman** - Documentação e testes das rotas

## 📂 Estrutura do Projeto

- `src/` - Contém o código-fonte Laravel
- `pgdata/` - Dados do banco PostgreSQL
- `miniodata/` - Dados do MinIO
- `docker-compose.yml` - Configuração do ambiente Docker
- `Dockerfile` - Configuração do contêiner da aplicação
- `vendor/` - Dependências do Laravel

## 🔑 Autenticação

A API utiliza autenticação baseada em **JWT (JSON Web Tokens)**. Para acessar os endpoints protegidos, é necessário autenticar-se e fornecer o token no cabeçalho das requisições.

[📄 Documentação da Autenticação](https://documenter.getpostman.com/view/41683423/2sB2cRC4R4)

## 📌 Funcionalidades Principais

### 📍 Lotação
Gerenciamento das lotações dos servidores.
[📄 Documentação da Lotação](https://documenter.getpostman.com/view/41683423/2sB2cRC4R5)

### 👤 Servidor Efetivo
Gerenciamento dos servidores efetivos.
[📄 Documentação do Servidor Efetivo](https://documenter.getpostman.com/view/41683423/2sB2cRC4VM)

### ⏳ Servidor Temporário
Gerenciamento dos servidores temporários.
[📄 Documentação do Servidor Temporário](https://documenter.getpostman.com/view/41683423/2sB2cRC4VN)

### 🏢 Unidade
Gerenciamento das unidades organizacionais.
[📄 Documentação da Unidade](https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ)

### 📷 Upload de Fotografias
Upload e gerenciamento de fotografias dos servidores.
[📄 Documentação do Upload de Fotografias](https://documenter.getpostman.com/view/41683423/2sB2cRC4VS)

## 🚀 Como Executar o Projeto

1. Clone o repositório:
   ```sh
   git clone https://github.com/attairsilva/projetoapirest.git
   ```

2. Acesse a pasta do projeto:
   ```sh
   cd projetoapirest
   ```

3. Suba os contêineres com Docker Compose:
   ```sh
   docker-compose up -d
   ```

4. A API estará disponível em `http://127.0.0.1:8000`


---

📧 **Contato:** attair@hotmail.com