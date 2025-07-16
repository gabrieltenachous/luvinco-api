## 📦 LuvinCo API

API RESTful em Laravel 12 para vitrine de produtos, carrinho de compras e histórico de pedidos.

> ⚠️ Requer token de autenticação (`Authorization`) para acessar qualquer rota protegida.

---

## 🚀 Como rodar com Docker

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/luvinco-api.git
cd luvinco-api
```

### 2. Crie o `.env`

```bash
cp .env.example .env
```

Edite os valores de conexão com o banco se necessário:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=luvinco
DB_USERNAME=root
DB_PASSWORD=root
```

### 3. Suba os containers

```bash
docker compose up --build
```

### 4. Acesse o container Laravel

```bash
docker exec -it luvinco-api bash
```

### 5. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 6. Rode as migrations e seeders

```bash
php artisan migrate --seed
```

### 7. Pronto! Acesse a API em:

```
http://localhost:8000
```

---

## 🧪 Autenticação

Todas as rotas requerem o seguinte **header obrigatório**:

```http
Authorization: wQ8ehU2x4gj93CH9lMTnelQO3GcFvLzyqn8Fj3WA0ffQy57I60
Accept: application/json
Content-Type: application/json
```

---

## 📘 Documentação Swagger

Disponível em:

```
http://localhost:8000/api/documentation
```

---

## 🔁 Endpoints da API

Todas as rotas estão protegidas pelo middleware `EnsureTokenIsValid`.

---

### ✅ `GET /api/products`

Lista os produtos disponíveis com filtros opcionais:

| Filtro     | Tipo   | Exemplo                         |
| ---------- | ------ | ------------------------------- |
| `name`     | string | `/api/products?name=camisa`     |
| `brand`    | string | `/api/products?brand=zara`      |
| `category` | string | `/api/products?category=Roupas` |

#### Response:

```json
{
  "message": "Products retrieved successfully.",
  "data": [ ... ]
}
```

---

### 🛒 `POST /api/orders`

Cria ou atualiza o **carrinho**.

#### Payload:

```json
{
  "items": [
    {
      "product_id": "UUID",
      "quantity": 2
    },
    {
      "product_id": "UUID",
      "quantity": 1
    }
  ]
}
```

Ou para **limpar o carrinho**:

```json
{
  "clear": true
}
```

#### Response:

```json
{
  "message": "Cart updated successfully.",
  "data": { ... }
}
```

---

### 🧺 `GET /api/orders`

Retorna o carrinho atual com status `aberto`.

---

### 📦 `POST /api/order-products`

Finaliza o carrinho e envia para o gateway externo.

* Desconta estoque dos produtos.
* Limpa o carrinho.
* Cria pedido no histórico.

#### Response:

```json
{
  "message": "Order finalized successfully.",
  "data": { ... }
}
```

---

### 📜 `GET /api/orders/completed`

Retorna a lista de **pedidos finalizados**.

Paginação:

```
GET /api/orders/completed?page=1
```

#### Response:

```json
{
  "message": "Completed orders retrieved successfully.",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    ...
  },
  "links": {
    "next": "...",
    "prev": "..."
  }
}
```

---

### 🧾 `GET /api/order-products?order_id=UUID`

Lista os produtos de um pedido finalizado.

---

## 📌 Commits sugeridos

```bash
docs: criar README com instruções para rodar a API via Docker
docs: documentar endpoints da API com exemplos de payload e headers
```

---

Se quiser, posso gerar esse conteúdo automaticamente no seu `README.md` com Markdown já formatado. Deseja que eu salve isso como arquivo também?
