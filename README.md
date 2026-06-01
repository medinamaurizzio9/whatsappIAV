# VIANKA GOLD MINING CRM WhatsApp

Base administrativa en Laravel 12 para probar el flujo inteligente de atencion y derivacion por WhatsApp sin conectar todavia WhatsApp Cloud API ni proveedores externos de IA.

## Requisitos

- PHP 8.3+
- Composer
- MySQL 8+
- Laravel 12

## Instalacion local

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configura la base en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vianka_gold_crm
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base y carga datos iniciales:

```bash
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS vianka_gold_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

## Accesos de prueba

- Administrador: `admin@viankagold.test` / `password`
- Operador: `operador@viankagold.test` / `password`

## Modulos incluidos

- Login y roles basicos: administrador y operador.
- Dashboard con conversaciones simuladas, clientes, derivaciones y pruebas de IA.
- Configuracion general de empresa, logo, telefono, direccion, horario y bienvenida.
- CRUD de menu inicial de WhatsApp.
- CRUD de areas de derivacion.
- Modulo de clientes.
- Chat interno de prueba con historial.
- Conversaciones simuladas.
- `App\Services\AiRouterService` para decidir IA simulada o derivacion.
- Base de conocimiento: categorias, documentos, FAQs, productos, promociones y sorteos.
- Buscador interno IA con respuestas locales simuladas y auditoria.
- Intenciones configurables para clasificar consultas, contenidos y futuras reglas de IA.
- Interfaces `AiProviderInterface` y `KnowledgeProviderInterface` preparadas para GPT, DeepSeek o Gemini.

## Reglas simuladas de derivacion

- Comprar joya: IA por defecto; deriva a Ventas si el texto contiene `comprar`, `pagar`, `precio` o `reserva`.
- Trabajar: IA por defecto; deriva a Supervisor Comercial si contiene `registrarme`, `afiliarme` o `vender`.
- Invertir: deriva siempre a Gerencia Comercial.
- Consulta post-compra: IA por defecto; deriva a Soporte si contiene `reclamo`, `problema`, `garantia` o `no recibi`.

## Verificacion

```bash
php artisan test
php artisan migrate:status
```

## Fase 2: base de conocimiento

El buscador interno esta disponible en `/buscador-ia`. Por ahora no llama APIs externas: usa `App\Services\KnowledgeBaseService` para buscar coincidencias en FAQs, productos, documentos, promociones y sorteos activos, construir una respuesta local y registrar la auditoria en `knowledge_queries`.

## Fase 2.5: intenciones

El modulo `/intentions` permite configurar etiquetas de intencion con accion por defecto, confianza minima, area de derivacion, palabras clave y prompt especifico opcional. FAQs, documentos, productos, promociones y sorteos pueden relacionarse con una o varias intenciones.

`KnowledgeBaseService` detecta intenciones localmente con palabras clave, busca contenido asociado y devuelve accion recomendada, confianza simulada y area sugerida. Todavia no se conecta OpenAI, DeepSeek, Gemini ni WhatsApp real.

## Fase 4: WhatsApp Cloud API oficial

La configuracion esta disponible en `/whatsapp/settings`. Guarda el `access_token` cifrado, muestra solo los ultimos 4 caracteres y permite definir el modo de atencion: manual, supervisado o automatico.

Para prueba local con ngrok:

```bash
php artisan serve
ngrok http 8000
```

Configura en Meta la URL:

```text
https://TU-NGROK/webhook/whatsapp
```

Comandos utiles:

```bash
php artisan whatsapp:diagnose
php artisan whatsapp:test-send 59170000000 "Mensaje de prueba"
```

El webhook recibe texto, imagen, audio, video, documento y ubicacion. No usa QR ni WhatsApp Web.
