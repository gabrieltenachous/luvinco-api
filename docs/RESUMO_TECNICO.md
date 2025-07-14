# Resumo Técnico do Projeto LuvinCo

Este documento apresenta um panorama de todas as decisões e implementações de código realizadas durante o Desafio Técnico LuvinCo, detalhando por que cada escolha foi feita e como cada componente se encaixa na arquitetura.

---

## 1. Arquitetura e Padrões

* **Service–Repository Pattern**: Separa lógica de negócio (Service) de acesso a dados (Repository). Assim, facilitamos:

  * Testes isolados de cada camada.
  * Substituição de implementações (ex.: outro banco).
  * Responsabilidade única.

* **Controllers finos**: Cada Controller orquestra chamadas ao Service e retorna respostas padronizadas com `ApiResponder`.

* **Resource** (`JsonResource`/`ResourceCollection`): padroniza formato JSON e encapsula lógica de serialização (ex.: `OrderResource`, `OrderProductResource`).

* **UUID nas Models**: Uso de `HasUuids` para chaves primárias não previsíveis e sem incremento, aumentando segurança e compatibilidade distribuída.

---

## 2. Fluxo de Dados

1. **Request** → Controller → Service → Repository → Model
2. **Response** do Model → Resource → `ApiResponder` → JSON unificado com *message + data*

Essa cadeia garante coesão e clareza na responsabilidade de cada camada.

---

## 3. Endpoints e Middleware

* **Roteamento em \*\*\*\*`routes/api.php`**: agrupado por `Route::middleware(EnsureTokenIsValid::class)->group(...)` para proteger as rotas com token.
* **`EnsureTokenIsValid`**: middleware customizado que valida header `Authorization`.
* Swagger e rotas de documentação (`/api/documentation`) são livres de autenticação.

---

## 4. Integrações Externas

* **`ProductGatewayService`**: encapsula `Http::get('https://luvinco.proxy.beeceptor.com/products')` usado no Seeder.
* **`OrderGatewayService`**: encapsula `Http::post('https://luvinco.proxy.beeceptor.com/orders')` no fluxo de finalização.
* **Transação** (`DB::transaction`): garante rollback se a integração externa falhar.

---

## 5. Seeders

* **`ProductSeeder`**: usa `ProductGatewayService` para importar do mock externo e `Product::updateOrCreate()`.
* Permite manter dados consistentes e idempotentes.

---

## 6. Swagger (OpenAPI)

* **L5-Swagger** configurado para gerar documentação automática.
* **`@OA\Info`** global em `app/OpenApi/ApiDoc.php`.
* Anotações em Controllers (`@OA\Get`, `@OA\Post`, `security`, `@OA\Schema`) para:

  * Descrever parâmetros, requestBody, responses.
  * Definir `SecurityScheme` para o token.

---

## 7. Testes Unitários

* **`OrderServiceTest`\*\*\*\*, ****`OrderProductServiceTest`****, \*\*\*\*`ProductServiceTest`**:

  * Cobertura de regras de negócio: criação de pedidos, validações de estoque, agregações.
  * Uso de `Attributes\Test` e `RefreshDatabase` para consistência.

---

## 8. Testes de Feature

* **`ProductApiTest`\*\*\*\*, ****`OrderApiTest`****, \*\*\*\*`OrderProductApiTest`**:

  * Testam endpoints via HTTP: status, estrutura JSON (`assertJsonStructure`, `assertJsonPath`).

---

## 9. Artisan Commands Customizados

* **`MakeServiceCommand`** (`app/Console/Commands/MakeServiceCommand.php`): gera esqueleto de Service e interface.
* **`MakeRepositoryCommand`** (`app/Console/Commands/MakeRepositoryCommand.php`): gera esqueleto de Repository.

Esses comandos aceleram criação de camadas, mantendo padrão de nomenclatura e diretórios.

---

## 10. Conclusão

Seguindo esta estrutura, alcançamos:

* Código **manutenível** e **testável**.
* **Separação de responsabilidades** clara.
* **Documentação automatizada** e consumível por front-end e clientes HTTP.
* **Flexibilidade** para trocar implementações externas (ex.: outro mock) sem tocar Controllers.

Este modelo serve de base para projetos escaláveis e de alta qualidade.
