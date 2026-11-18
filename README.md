# CBT Veterinaria

Sistema de gestão veterinária desenvolvido em PHP, com base de dados MariaDB/MySQL e ambiente de execução preparado com Docker.

O objetivo do projeto é disponibilizar uma aplicação web para apoiar a gestão das operações de uma clínica veterinária. A aplicação está organizada em páginas, ações, recursos visuais e componentes de ligação à base de dados.

## Tecnologias utilizadas

- **PHP** — lógica da aplicação no servidor.
- **HTML, CSS e JavaScript** — estrutura, apresentação e interatividade da interface.
- **MariaDB / MySQL** — gestão e armazenamento dos dados.
- **Docker e Docker Compose** — criação e execução de um ambiente consistente.
- **Apache** — servidor web utilizado pelo contentor PHP.
- **phpMyAdmin** — administração visual da base de dados.
- **Git e GitHub** — controlo de versões e partilha do código.

## Estrutura do projeto

A estrutura identificada no repositório é semelhante à seguinte:

```text
cbt_veterinaria/
├── .env.example
├── .gitignore
├── Dockerfile
├── docker-compose.yml
├── index.php
└── trabalho final/
    ├── actions/       # Processamento de ações e operações da aplicação
    ├── assets/        # Recursos estáticos, como CSS, JavaScript e imagens
    ├── includes/      # Componentes reutilizáveis e ligação à base de dados
    │   └── conexao.php
    └── pages/         # Páginas da aplicação
```

> A organização e os nomes dos ficheiros podem evoluir durante o desenvolvimento. Confirma a estrutura atual do repositório antes de acrescentar novos módulos a esta documentação.

## Requisitos

Para executar o projeto através de Docker, é necessário:

- Docker Desktop instalado e em execução;
- Git, caso pretendas clonar o repositório;
- Um navegador web.

Não é necessário instalar PHP ou MariaDB diretamente no computador quando utilizas os contentores definidos no Docker Compose.

## Instalação e execução

### 1. Clonar o repositório

```bash
git clone https://github.com/Piolioz/cbt_veterinaria.git
cd cbt_veterinaria
```

### 2. Criar o ficheiro de ambiente

Na pasta principal do projeto, cria uma cópia do ficheiro de exemplo.

No PowerShell:

```powershell
Copy-Item .env.example .env
```

No Git Bash:

```bash
cp .env.example .env
```

Se o ficheiro `.env.example` não estiver presente, confirma que estás na pasta principal do repositório.

### 3. Configurar as variáveis

Abre o ficheiro `.env` e define os valores adequados:

```env
DB_ROOT_PASSWORD=altera_esta_password
DB_NAME=cbt_veterinaria
DB_USER=admin
DB_PASSWORD=altera_esta_password
```

Utiliza palavras-passe fortes e próprias para cada ambiente. O ficheiro `.env` contém credenciais e não deve ser enviado para o GitHub.

### 4. Iniciar os serviços

Na pasta onde se encontra o `docker-compose.yml`, executa:

```bash
docker compose up -d --build
```

Verifica o estado dos contentores:

```bash
docker compose ps
```

### 5. Aceder à aplicação

Com a configuração de portas atualmente prevista:

| Serviço | Endereço |
|---|---|
| Aplicação web | http://localhost:8081 |
| phpMyAdmin | http://localhost:8082 |

Se os endereços ou portas tiverem sido alterados no `docker-compose.yml`, utiliza os valores definidos nesse ficheiro.

## Base de dados

A aplicação utiliza MariaDB/MySQL. A ligação PHP é configurada através de variáveis de ambiente, evitando colocar credenciais diretamente no código.

O serviço da base de dados é identificado na rede Docker pelo nome `db`. Por isso, a aplicação deve utilizar `db` como endereço do servidor da base de dados quando estiver a correr dentro dos contentores.

### Importar uma base de dados existente

1. Exporta a base de dados original em formato `.sql` a partir do phpMyAdmin ou de outra ferramenta compatível.
2. Abre o phpMyAdmin do ambiente Docker em http://localhost:8082.
3. Seleciona ou cria a base de dados `cbt_veterinaria`.
4. Abre o separador **Importar**.
5. Escolhe o ficheiro `.sql` e inicia a importação.

O ficheiro de exportação deve ser transferido por um canal seguro. Não publiques dados pessoais, clínicos ou de clientes num repositório público.

### Persistência dos dados

O Docker Compose utiliza um volume para guardar os dados da base de dados. Assim, os dados permanecem guardados mesmo que os contentores sejam recriados.

Para parar os serviços sem apagar o volume:

```bash
docker compose down
```

Para voltar a iniciar:

```bash
docker compose up -d
```

**Atenção:** `docker compose down -v` remove também os volumes associados e pode apagar os dados persistidos da base de dados. Não utilizes esse comando sem teres uma cópia de segurança.

## Desenvolvimento em vários computadores

O GitHub é utilizado para sincronizar o código-fonte e os ficheiros de configuração versionáveis. A base de dados que está dentro de um volume Docker não é sincronizada pelo Git.

Para preparar outro computador:

1. Clona o repositório.
2. Cria o respetivo ficheiro `.env` a partir de `.env.example`.
3. Inicia os serviços com Docker Compose.
4. Importa uma cópia de segurança `.sql`, se for necessário reproduzir os dados de desenvolvimento.

Cada computador pode ter a sua própria base de dados local. Se vários utilizadores precisarem de trabalhar simultaneamente sobre os mesmos dados, deve ser configurada uma instância de base de dados centralizada, com rede privada, autenticação, permissões e cópias de segurança.

## Comandos úteis

```bash
# Iniciar e construir os serviços
docker compose up -d --build

# Ver o estado dos serviços
docker compose ps

# Consultar os registos
docker compose logs -f

# Consultar os registos apenas do serviço web
docker compose logs -f web

# Parar os serviços sem apagar os dados
docker compose down

# Reconstruir os serviços
docker compose up -d --build
```

## Segurança

- Não enviar o ficheiro `.env` para o GitHub.
- Manter o `.env.example` sem credenciais reais.
- Não publicar cópias da base de dados com dados pessoais ou clínicos.
- Utilizar palavras-passe fortes e permissões mínimas.
- Fazer cópias de segurança regulares da base de dados.
- Em produção, utilizar HTTPS, restringir o acesso à base de dados e proteger os dados de acordo com a legislação aplicável, incluindo o RGPD.

## Estado do projeto

Este README documenta a estrutura e o ambiente técnico conhecidos do projeto. Os módulos funcionais concretos, regras de negócio, perfis de utilizador e funcionalidades implementadas devem ser atualizados à medida que forem confirmados no código.

## Repositório

[GitHub — cbt_veterinaria](https://github.com/Piolioz/cbt_veterinaria)
