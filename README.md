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

<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R4" target="_blank">📄 Documentação da Autenticação</a>

## 📌 Funcionalidades Principais

### 📍 Lotação
Gerenciamento das lotações dos servidores.
<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R5" target="_blank">📄 Documentação da Lotação</a>

### 👤 Servidor Efetivo
Gerenciamento dos servidores efetivos.
<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VM" target="_blank">📄 Documentação do Servidor Efetivo</a>

### ⏳ Servidor Temporário
Gerenciamento dos servidores temporários.
<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VN" target="_blank">📄 Documentação do Servidor Temporário</a>

### 🏢 Unidade
Gerenciamento das unidades organizacionais.
<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ" target="_blank">📄 Documentação da Unidade</a>

### 📷 Upload de Fotografias
Upload e gerenciamento de fotografias dos servidores.
<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VS" target="_blank">📄 Documentação do Upload de Fotografias</a>


## ⚙️ Pré-requisitos

1. Docker:

   Verifique se o Docker está instalado, se for Windows, execute os seguintes comandos no PowerShell:
   ```
      docker --version
      docker-compose --version

   ```
   Se o Docker não estiver instalado, baixe e instale o Docker Desktop.

2. WSL 2 (Windows 10/11):

   Habilite o WSL 2, essencial para o Docker Desktop. Se não estiver instalado, execute o seguinte comando no PowerShell:
   ```
      wsl --install
   ```
   Em seguida, execute:
   ```
      Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
   ```

3. Composer:

   Instale e configure, se windows: https://getcomposer.org/Composer-Setup.exe 
   Não esqueça de configurar a varável de ambiente.

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

4. Para encerrar os contêineres, com Docker Compose:
   ```sh
   docker-compose down
   ```

## 🚀 CAMINNHOS DA API

   A API estará disponível em `http://127.0.0.1:8000` e funcionará para os métodos GET, POST, PUT e DELETE conforme documentção.

   Minio ObjectSore em `http://127.0.0.1:9001` acesso pelo navegador.


## 🚀 OBSERVAÇÃO ENDPOINT MINIO

   O Minio esta habilitado com os endpoints abaixo

   ```
   AWS_ENDPOINT=http://localhost:9000
   AWS_ENDPOINT_ENVIA=http://minio:9000
   ```

## 🚀 Observações a respeito do Docker

   Iniciar os containers em Docker:

   ```  
   docker-compose up --build -d 
   ``` 
   Executa os containers
   '--build' constroi as imagens definida no docker-compose.yml
   '-d' pede que a execução ocorra em segundo plano (opcional)

   Para os containers em Docker:
   ```   
      docker-compose down -v     
       ``` 
   '-v' opcional (a não ser que deseje remover os volumes)
   A ação para e remove os contêineres, redes criadas, volumes nomeados no arquivo docker-compose.yml.


## 🚀  Comandos que podem ser necessários (obs: com container em execução):

   ``` 
   docker-compose exec app php artisan migrate:fresh --seed 
   ```
   O migrate:fresh apaga todas as tabelas e recria do zero o banco de dados antes de rodar os seeders, o seeders preenche o banco automático com dados aleatórios. Para não preencher o banco e mante-lo vazio voce pode subtrair o '--seed'

   No projeto está mantida a rota '/api/auth/registrar' para registrar um novo usuário para os casos de reset do banco:

   ```
      {
         "name": "Administrador",
         "email": "admin@admin.com",
         "password": "123456"
      } 
   ```

  




---

📧 **Contato:** attair@hotmail.com