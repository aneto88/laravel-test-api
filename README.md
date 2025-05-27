
# Laravel 12 + Docker Compose

Este projeto utiliza Docker Compose para facilitar o desenvolvimento com Laravel 12.

## Pré-requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- (Opcional) [Make](https://www.gnu.org/software/make/) para facilitar comandos no Unix/Linux/Mac

---

## Como rodar o projeto

1. **Gerar o env:**
   ```sh
   cp .env-example .env
   ```
   
### Usando Make (Unix/Linux/Mac)

1. **Fazer o build ds containers:**
   ```sh
   make build
   ```

2. **Instalar dependências do PHP:**
   ```sh
   make composer-install
   ```

3. **Rodar migrations:**
   ```sh
   make migrate
   ```

4. **Subir os containers:**
   ```sh
   make up
   ```

5. **Acessar o container:(opcional)**
   ```sh
   make bash
   ```

6. **Rodar os testes:**
   ```sh
   make test
   ```

6. **Outros comandos úteis:**
    - `make artisan` — Executa comandos do Artisan (exemplo: `make artisan migrate`)
    - `make logs` — Mostra os logs dos containers

---

### Usando Docker Compose diretamente (Unix/Windows)

1. **Fazer o build ds containers:
   :**
   ```sh
   docker compose -f compose.dev.yaml up -d
   ```

2. **Instalar dependências do PHP:**
   ```sh
   docker compose -f compose.dev.yaml exec workspace composer install
   ```

3. **Rodar migrations:**
   ```sh
   docker compose -f compose.dev.yaml exec workspace php artisan migrate
   ```

4. **Subir os containers:**
   ```sh
   docker compose -f compose.dev.yaml up -d
   ```

5. **Acessar o container:**
   ```sh
   docker compose -f compose.dev.yaml exec workspace bash
   ```

6. **Rodar os testes:**
   ```sh
   docker compose -f compose.dev.yaml exec workspace php artisan test
   ```
7. **Outros comandos úteis:**
    - `docker compose -f compose.dev.yaml exec workspace php artisan <comando>`
    - `docker compose -f compose.dev.yaml exec workspace npm run dev`
    - `docker compose -f compose.dev.yaml logs -f`
    - `docker compose -f compose.dev.yaml down` (para parar os containers)

---

## Documentação da API

A documentação da API Swagger pode ser acessada em:

```
http://localhost/api/doc
```


# Decisões de Design: API de Clientes e Produtos Favoritos

## Contexto

A API de clientes possui uma funcionalidade de listagem de produtos favoritos, cujos dados completos são mantidos em uma API externa.

## Abordagem Adotada

Para otimizar a performance e garantir a disponibilidade dos dados, foi decidido trazer dados parciais dos produtos favoritos para o banco de dados local.

## Prós

- **Redução da Latência:** Evita a necessidade de consultar a API externa a cada requisição, diminuindo a latência e melhorando a experiência do usuário.
- **Disponibilidade:** Garante que os dados dos produtos favoritos estejam disponíveis mesmo em caso de indisponibilidade temporária da API externa.

## Contras

- **Sincronização de Dados:** Requer um mecanismo para manter os dados locais sincronizados com a API externa, o que pode adicionar complexidade ao sistema.
- **Dependência da API Externa:** Embora os dados parciais estejam armazenados localmente, ainda existe uma dependência da API externa para obter informações completas ou atualizações.
- **Exclusão de Produtos:** Caso um produto seja excluído na API externa, a aplicação não será notificada automaticamente, podendo exibir informações desatualizadas.

## Possíveis Soluções e Melhorias

1. **Cache com Redis:** Implementar uma camada de cache com Redis na frente da API externa pode mitigar a dependência e melhorar a performance. O Redis pode armazenar os dados dos produtos favoritos por um determinado período, reduzindo a necessidade de consultar a API externa com frequência.

2. **Webhooks ou Eventos:** Utilizar webhooks ou eventos da API externa para notificar a aplicação sobre alterações nos produtos favoritos, como exclusões ou atualizações. Isso permite manter os dados locais sincronizados de forma mais eficiente.

3. **Job de Sincronização:** Criar um job agendado para sincronizar os dados dos produtos favoritos com a API externa periodicamente. Esse job pode verificar se houve alterações nos produtos e atualizar o banco de dados local de acordo.


## Dicas

- Para gerar a chave da aplicação:
  ```sh
  make key-generate
  # ou
  docker compose -f compose.dev.yaml exec workspace php artisan key:generate
  ```

- Para limpar caches:
  ```sh
  make cache-clear
  # ou
  docker compose -f compose.dev.yaml exec workspace php artisan cache:clear
  ```

