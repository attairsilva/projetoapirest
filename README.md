# Projeto API REST em PhP 

Este repositório contém a implementação de uma API desenvolvida em (PHP) framework Laravel para gerenciamento de servidores, unidades e lotações.

Foi criado para atender o PSS 02/2025/SEPLAG.

Nome: Attair Batista da Silva
CPF: 692.7*****-34

## 📌 Tecnologias Utilizadas

- **Laravel** - Framework PHP para desenvolvimento backend
- **PostgreSQL** - Banco de dados relacional
- **MinIO** - Armazenamento de objetos compatível com S3
- **Docker** - Contêineres - v27.5.1
- **Docker Compose** - v2.18.0


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

   ## WINDOWS

   #### Intalando Docker e Docker-Compose:
   
   Verifique se o Docker está instalado, execute o seguinte comandos no  ```PowerShell```:
   ```
      docker --version
     
   ```
    
   Se o Docker não estiver instalado, baixe e instale o Docker Desktop.
   ```
      https://docs.docker.com/desktop/setup/install/windows-install/
   ```

   Agora verifique se o docker-compose foi instalado, execute o seguinte comandos no  ```PowerShell```:
   ```
       docker-compose --version
   ```

   Durante a instalação do Docker Desktop, se deixou marcado WSL no instalador, tudo está certo, porém podem ocorrer problemas com o WSL em razão da necessidade de virtualização e do Hyper-V, e se este é o seu caso, abra o ```PowerShell``` em modo administrador e execute: 
   ```
      wsl --install
   ```

   Caso encontre um erro, é poque exigiu que  habilite os recursos de 'Máquina Virtual do Windows', existem instruções mais detalhadas em https://aka.ms/wsl2-install. 
   
   Verifique se a virtualização na BIOS de seu equipamento está ativada, e depois habilite o Virtual Machine Platform no Windows, execute no  ```PowerShell``` o comandos:

   ```
      dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
   ```
   
   ```
      dism.exe /online /enable-feature /featurename:Microsoft-Hyper-V-All /all /norestart
   ```

   ```
      Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
   ```
   Após o comando anterior, será solicitado que reinicio o computador.

   Se mesmo habilitando o suporte a virtualização na Bios, não obteve sucesso, pode ser que sua versão do Windows não cumpre os requisitos para o Hyper-V, é necessário uma versão que suporte.

   Agora, se tudo correu bem até aqui, execute no ```PowerShell``` modo administrador, o seguinte comando novamente: ```wsl --install``` 

   Depois de resolver a habilitação do WLS, Hyper-V, Máquina Virtual, caso o ```docker-compose``` não esteja instalado, no ```PowerShell``` modo administrador, execute o seguinte comando:

   ```
     Start-BitsTransfer -Source "https://github.com/docker/compose/releases/download/v2.34.0/docker-compose-windows-x86_64.exe" -Destination $Env:ProgramFiles\Docker\docker-compose.exe
   ```

   #### BAIXANDO O PROJETO

   Baixe o repositório do Projeto:
   - https://github.com/attairsilva/projetoapirest/archive/refs/heads/main.zip
   - Descompacte em uma pasta do sistema.


   ## LINUX UBUNTU

   #### Instalando Docker e Docker-Compose:

   Utilizando o script de conveniência disponível https://get.docker.com/ para instalação de Docker (não recomendado para produçao)
   
   Atualiza a lista de pacotes disponíveis nos repositório:
   ```
      sudo apt update && sudo apt upgrade -y
   ```

   Instale o Curl se não tiver instalado
   ```
      sudo apt install curl:
   ```

   Instale o Curl se não tiver instalado
   ```
      sudo apt install curl:
   ```

   Execute o comando abaixo para realizar a instsalação:
   ```
      curl -fsSL https://get.docker.com -o get-docker.sh
      sudo sh get-docker.sh
   ```

   Execute o comando abaixo para realizar a instsalação do Docker-Compose na versão 2.32.4 ou superior:
   ```
      sudo curl -L "https://github.com/docker/compose/releases/download/v2.18.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   ```

   Interessante criar links simbólicos para evitar erros de caminho ao executar docker-compose:
   ```
      sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
   ```

   É preciso aplicar permissões:
   ```
      sudo chmod +x /usr/local/bin/docker-compose
   ```

   Execute os comandos abaixo para certificar que foram instalados:
   ```
      docker --version
      docker-compose --version
   ```

   #### CLONNANDO O PROJETO 

   Crie uma pasta e acesse:
   ```
     mkdir Projetos
     cd Projetos
   ```

   Clone o repositório do projeto:
   ```
      git clone https://github.com/attairsilva/projetoapirest.git 
   ```

   Acesse o diretório  criado dentro de projeto:
   ```
      cd projetoapirest
   ```


## 📌 Execução do Docker

1. Inicie o ```Docker Desktop``` antes de prosseguir.

2. Suba os contêineres com Docker Compose no ```CMD``` ou ```PowerShell``` executando o código abaixo quando estiver na pasta do projeto:
   ```
   sudo docker-compose up 
   ```

5. Para encerrar os contêineres, com execute:
   ```
   sudo docker-compose down
   ```

### 📌  USO DA API

   A API estará disponível em `http://127.0.0.1:8000` e funcionará para os métodos GET, POST, PUT e DELETE conforme documentção. (POSTMAN)

   O cabeçalho deve conter dados de login conforme documentação.
   Existe um usuário padrão já preenchido pelo seeder:
   ```Usuário: admin@admin.com.br```
   ```Senha: 123456```

   Quanto ao Minio ObjectSore está disponível em `http://127.0.0.1:9001` acesso pelo navegador.
   ```Usuário: admin```
   ```Senha: adminpassword```

   A documentação completa de uso da API estão nas primeiras instruções dete Readme.


### 🚀 Recriando Containers

   Abaixo comandos para apagar volumes e recriar imagens. Este procedimento pode fazer com que sejam necessários ajustar as configurações novamente para que a API funcione. Tipo:

   1. Ecerrando Docker
      ```   
         docker-compose down -v     
      ```
      "-v" opcional (a não ser que deseje remover os volumes)
      A ação para e remove os contêineres, redes criadas, volumes nomeados no arquivo docker-compose.yml.

   2. Iniciando Docker com docker-compose.yml
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
      ```
        Acesse a área de administrador 
        Minio http://127.0.0.1:9001/ 
        Login: admin 
        Senha: adminpassword
      ```
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

       No menu "Access Keys" no administrador do Minio, gere as chaves  e copie, substituindo no  ``` /src/env.renomeie ```:
      ```
         AWS_ACCESS_KEY_ID=Codigo do Access Key
         AWS_SECRET_ACCESS_KEY=Codigo do Secret Key
      ```

      Encerre o conainer:

      ```   
         docker-compose down -v     
      ```

      Inicia novamente utilizando   ``` --build ```:
      
      ```  
         docker-compose up --build -d 
      ``` 


---

📧 **Contato:** attair@hotmail.com