# Projeto API REST em PhP 

Este repositório contém a implementação de uma API desenvolvida em (PHP) framework Laravel para gerenciamento de servidores, unidades e lotações.

Foi criado para atender o PSS 02/2025/SEPLAG.

Nome: Attair Batista da Silva
CPF: 692.7*****-34

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

## <a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R4" target="_blank">📄 Documentação da Autenticação</a>

## 📌 Funcionalidades Principais

### 👤 Servidor Efetivo 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VM" target="_blank">Documentação</a>
Esta endpoints gerencia Servidores Efetivos.

### 👤 Servidor Efetivo Busca pelo Nome 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VM#da8ecda6-47fd-4d7d-bdb9-4b4ebf565ba5" target="_blank">Documentação</a>
Este endpoint é usado para buscar servidores efetivos pelo nome, com paginação. O seu retorno é o endereço funcional do servidor.

### 👤 Servidor Temporário 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VN" target="_blank">Documentação</a>
Este endpoint para gerencia os servidores temporários.

### 👤 Servidor Efetivo Lotados em Unidade 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ#4cdc92a3-3395-4231-8f50-8c1c51d13da4" target="_blank"> Documentação</a>
Este endpoint é usado para listar os servidores efetivos lotados em uma unidade específica, com paginação.

### 🏢 Unidade 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VQ" target="_blank">Documentação</a>
Endpoint para gerenciamento das unidades organizacionais.

### 📍 Lotação 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4R5" target="_blank">Documentação</a>
Esta endpoints que gerencia as lotações dos servidores. 

### 📷 Upload de Fotografias 📄<a href="https://documenter.getpostman.com/view/41683423/2sB2cRC4VS" target="_blank">Documentação</a>
Esta endpoints da API gerencia uploads de fotografias para pessoas, sejam Servidores Efetivos e Temporários. Envio de fotografia, deleção, recuperação com links temporários. 


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


## 📌 Como Executar o Projeto

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

### 📌  USO DA API

   A API estará disponível em `http://127.0.0.1:8000` e funcionará para os métodos GET, POST, PUT e DELETE conforme documentção. (POSTMAN)
   Minio ObjectSore em `http://127.0.0.1:9001` acesso pelo navegador.
   Documentação completa de uso da API estão nas primeiras instruções dete Readme.


### 🚀 Recriando Containers

   Abaixo estão comendos para apagar volumes e recriar imagens. Este procedimento pode fazer com que sejam necessários ajustar as configurações novamente para que a API funcione. Alguns constam abaixo:

   1. Ecerrando Docker
      ```   
         docker-compose down -v     
      ```
      "-v" opcional (a não ser que deseje remover os volumes)
      A ação para e remove os contêineres, redes criadas, volumes nomeados no arquivo docker-compose.yml.

   2. Iniciando Docker com docker-compos.yml
      ```  
         docker-compose up --build -d 
      ``` 
      "--build" constroi as imagens definida no docker-compose.yml
      "-d" pede que a execução ocorra em segundo plano (opcional)

   3. Quando for preciso recriar o banco de dados, execute, quando o contair estiver ativo, o comando abaixo, que acessa o containr e executa o ```php artisan migrate:fresh --seed``` :
      ```
      docker-compose exec app php artisan migrate:fresh --seed
      ```
      O migrate:fresh apaga todas as tabelas e recria do zero o banco de dados antes de rodar os seeders, o seeders preenche o banco automático com dados aleatórios. Para não preencher o banco e mante-lo vazio voce pode subtrair o '--seed'

   4. Se você apagou a pasta 'miniodata', crie novamente, e inicio o container.
   
      Acesse a área de administrador do Minio http://127.0.0.1:9001/ - login: admin - senha: adminpassword
      
      Crie o bucket 'Uploads', public ou personalize. Recomendo utilizar a Política abaixo para o Bucket a ser criado:

      ```
                  {
               "Version": "2012-10-17",
               "Statement": [
                  {
                        "Effect": "Allow",
                        "Principal": {
                           "AWS": [
                              "*"
                           ]
                        },
                        "Action": [
                           "s3:PutObject"
                        ],
                        "Resource": [
                           "arn:aws:s3:::uploads/*"
                        ]
                  },
                  {
                        "Effect": "Allow",
                        "Principal": {
                           "AWS": [
                              "*"
                           ]
                        },
                        "Action": [
                           "s3:GetObject"
                        ],
                        "Resource": [
                           "arn:aws:s3:::uploads/*"
                        ]
                  }
               ]
            }
      ```

       No menu "Access Keys" na administrador do Minio, gere as chaves  e copie, substituindo no  ``` /src/.env ```:
      ```
         AWS_ACCESS_KEY_ID=Codigo do Access Key
         AWS_SECRET_ACCESS_KEY=Codigo do Secret Key
      ```

---

📧 **Contato:** attair@hotmail.com