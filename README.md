# Projeto API REST em Laravel

Bem-vindo ao **Projeto API REST em Laravel**! Este repositório contém a implementação de uma API desenvolvida em Laravel para gerenciamento de servidores, unidades e lotações.

## 📌 Tecnologias Utilizadas

- **Laravel** - Framework PHP para desenvolvimento backend
- **PostgreSQL** - Banco de dados relacional
- **MinIO** - Armazenamento de objetos compatível com S3
- **Docker** - Contêineres para ambiente isolado

## 📂 Estrutura do Projeto

- `miniodata/` - Dados do Minio
- `src/` - Contém o código-fonte Laravel
- `docker-compose.yml` - Configuração do ambiente Docker
- `Dockerfile` - Configuração do contêiner da aplicação
- `apache-laravel.conf` - Configuração personalizada apache
- `vendor/` - Dependências do Laravel
## 🔑 Autenticação

A API utiliza autenticação. Para acessar os endpoints protegidos, é necessário autenticar-se e fornecer o token no cabeçalho das requisições. Cada token gerado expira em 5 (cinco) minutos.

# <a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R4" target="_blank">📄 Documentação da Autenticação</a>

## 📌 Funcionalidades Principais

### 👤 Servidor Efetivo
Esta endpoints gerencia Servidores Efetivos. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VM" target="_blank">Documentação</a>

### 👤 Servidor Efetivo Busca pelo Nome
Este endpoint é usado para buscar servidores efetivos pelo nome, com paginação. O seu retorno é o endereço funcional do servidor. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VM#da8ecda6-47fd-4d7d-bdb9-4b4ebf565ba5" target="_blank">Documentação</a>

### ⏳ Servidor Temporário
Este endpoint para gerencia os servidores temporários. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VN" target="_blank">Documentação</a>

### 👤 Servidor Efetivo Lotados em Unidade
Este endpoint é usado para listar os servidores efetivos lotados em uma unidade específica, com paginação. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ#4cdc92a3-3395-4231-8f50-8c1c51d13da4" target="_blank"> Documentação</a>

### 🏢 Unidade
Endpoint para gerenciamento das unidades organizacionais. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ" target="_blank">Documentação</a>

### 📍 Lotação
Esta endpoints que gerencia as lotações dos servidores. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R5" target="_blank">Documentação da Lotação</a>

### 📷 Upload de Fotografias
Esta endpoints da API gerencia uploads de fotografias para pessoas, sejam Servidores Efetivos e Temporários. Envio de fotografia, deleção, recuperação com links temporários. 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VS" target="_blank"></a>


## ⚙️ Pré-requisitos

1. Docker & Docker-Compose:

   Verifique se o Docker está instalado, se for Windows, execute os seguintes comandos no PowerShell:
   ```
      docker --version
      docker-compose --version

   ```
   Se o Docker não estiver instalado, baixe e instale o Docker Desktop.
   Se o Docker-Compose não estiver instalado, baixe e instale (não esqueça da variável de ambiente - Windows - https://getcomposer.org/Composer-Setup.exe )

2. WSL 2 (Windows 10/11):

   Habilite o WSL 2, essencial para o Docker Desktop. Se não estiver instalado, execute o seguinte comando no PowerShell:
   ```
      wsl --install
   ```
   Em seguida, execute:
   ```
      Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
   ```


## 🚀 Como Executar o Projeto

1. Clone o repositório:
   ```sh
   git clone https://github.com/attairsilva/projetoapirest.git
   ```

2. Acesse a pasta do projeto:
   ```sh
   cd projetoapirest
   ```

2. Em /src, renomei o '.env.renomeie' para '.env'.

4. Suba os contêineres com Docker Compose:
   ```sh
   docker-compose up -d
   ```

5. Para encerrar os contêineres, com Docker Compose:
   ```sh
   docker-compose down
   ```

## 🚀 ACESSAR A API

   A API estará disponível em `http://127.0.0.1:8000` e funcionará para os métodos GET, POST, PUT e DELETE conforme documentção. (POSTMAN)

   Minio ObjectSore em `http://127.0.0.1:9001` acesso pelo navegador.


## 🚀 E SE EU PRECISAR RECRIAR AS IMAGENS DO CONTAINER?!

### Docker:

   ```  
       docker-compose up --build -d 
   ``` 
   "--build" constroi as imagens definida no docker-compose.yml
   "-d" pede que a execução ocorra em segundo plano (opcional)


   ```   
      docker-compose down -v     
   ```
   "-v" opcional (a não ser que deseje remover os volumes)
   A ação para e remove os contêineres, redes criadas, volumes nomeados no arquivo docker-compose.yml.

   Quando for preciso recriar o banco de dados, execute:
   ```
     docker-compose exec app php artisan migrate:fresh --seed
   ```
   O seeder preencherá o banco com dados eleatórios.


### Zerar o Banco de Dados / Recriar Banco (com Docker em execução):

   ``` 
   docker-compose exec app php artisan migrate:fresh --seed 
   ```
   O migrate:fresh apaga todas as tabelas e recria do zero o banco de dados antes de rodar os seeders, o seeders preenche o banco automático com dados aleatórios. Para não preencher o banco e mante-lo vazio voce pode subtrair o '--seed'


### Após recriado o container Minio

   Se você apagou a pasta 'miniodata', crie novamente, e inicio o container.
   
   1. Acesse a área de administrador do Minio
   
      http://127.0.0.1:9001/ - login: admin - senha: adminpassword
      
      Crie o bucket 'Uploads', public ou personalize.

      Gere as chaves no menu "Access Keys", copia o Access Key e o Secret Key e cole no '.env' que renomeou:
      ```
         AWS_ACCESS_KEY_ID=Codigo do Access Key
         AWS_SECRET_ACCESS_KEY=Codigo do Secret Key
      ```

   2. Pare o Container
      ``` 
         docker-compose down -v 
      ``` 

   3. Inicie o Container
      ```  
         docker-compose up --build -d 
      ``` 

---

📧 **Contato:** attair@hotmail.com