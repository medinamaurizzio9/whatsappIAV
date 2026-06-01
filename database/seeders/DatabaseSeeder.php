<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\AiPromptTemplate;
use App\Models\AiProviderSetting;
use App\Models\DerivationArea;
use App\Models\GeneralSetting;
use App\Models\InitialMenuOption;
use App\Models\Intention;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeFaq;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Raffle;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@viankagold.test'],
            [
                'name' => 'Administrador VIANKA',
                'role' => 'administrador',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'operador@viankagold.test'],
            [
                'name' => 'Operador VIANKA',
                'role' => 'operador',
                'password' => Hash::make('password'),
            ]
        );

        GeneralSetting::query()->firstOrCreate([], [
            'company_name' => 'VIANKA GOLD MINING',
            'main_phone' => '+591 70000000',
            'address' => 'Santa Cruz, Bolivia',
            'business_hours' => 'Lunes a sabado de 09:00 a 18:00',
            'welcome_message' => 'Bienvenido a VIANKA GOLD MINING. Selecciona una opcion para ayudarte.',
        ]);

        $areas = collect([
            ['name' => 'Ventas', 'description' => 'Atencion de compras, precios, pagos y reservas.', 'whatsapp_number' => '+591 70000001', 'email' => 'ventas@viankagold.test'],
            ['name' => 'Supervisor Comercial', 'description' => 'Registro, afiliacion y postulaciones comerciales.', 'whatsapp_number' => '+591 70000002', 'email' => 'supervisor@viankagold.test'],
            ['name' => 'Gerencia Comercial', 'description' => 'Consultas de inversion y oportunidades estrategicas.', 'whatsapp_number' => '+591 70000003', 'email' => 'gerencia@viankagold.test'],
            ['name' => 'Soporte', 'description' => 'Consultas post-compra, reclamos y garantias.', 'whatsapp_number' => '+591 70000004', 'email' => 'soporte@viankagold.test'],
        ])->mapWithKeys(fn (array $area) => [
            $area['name'] => DerivationArea::updateOrCreate(['name' => $area['name']], $area + ['is_active' => true]),
        ]);

        $options = [
            ['sort_order' => 1, 'title' => 'Quiero comprar una joya', 'description' => 'Consulta de compra, catalogo, precios o reservas.', 'action' => InitialMenuOption::ACTION_IA, 'derivation_area_id' => $areas['Ventas']->id],
            ['sort_order' => 2, 'title' => 'Quiero trabajar con VIANKA GOLD MINING', 'description' => 'Informacion para interesadas/os en trabajar o vender.', 'action' => InitialMenuOption::ACTION_IA, 'derivation_area_id' => $areas['Supervisor Comercial']->id],
            ['sort_order' => 3, 'title' => 'Quiero invertir', 'description' => 'Consultas sobre inversion.', 'action' => InitialMenuOption::ACTION_DERIVATION, 'derivation_area_id' => $areas['Gerencia Comercial']->id],
            ['sort_order' => 4, 'title' => 'Ya compre y tengo una consulta', 'description' => 'Atencion post-compra y soporte.', 'action' => InitialMenuOption::ACTION_IA, 'derivation_area_id' => $areas['Soporte']->id],
        ];

        foreach ($options as $option) {
            InitialMenuOption::updateOrCreate(['sort_order' => $option['sort_order']], $option + ['is_active' => true]);
        }

        Client::updateOrCreate(
            ['phone' => '+591 75555555'],
            ['name' => 'Cliente de prueba', 'city' => 'Santa Cruz', 'type' => 'prospecto', 'observations' => 'Cliente demo para probar el chat interno.']
        );

        $knowledgeCategories = collect([
            'Productos',
            'Joyas',
            'Sorteos',
            'Promociones',
            'Inversiones',
            'Afiliaciones',
            'Preguntas frecuentes',
            'Documentos legales',
            'Ubicacion y contacto',
        ])->mapWithKeys(fn (string $name) => [
            $name => KnowledgeCategory::updateOrCreate(
                ['name' => $name],
                ['description' => "Informacion de {$name} para respuestas internas.", 'is_active' => true]
            ),
        ]);

        KnowledgeFaq::updateOrCreate(
            ['question' => 'Donde esta ubicada VIANKA GOLD MINING?'],
            [
                'knowledge_category_id' => $knowledgeCategories['Ubicacion y contacto']->id,
                'answer' => 'VIANKA GOLD MINING atiende desde Santa Cruz, Bolivia. Puedes consultar la direccion exacta y horarios en la configuracion general.',
                'keywords' => 'ubicacion direccion contacto santa cruz horario',
                'priority' => 10,
                'is_active' => true,
            ]
        );

        KnowledgeFaq::updateOrCreate(
            ['question' => 'Como puedo afiliarme para trabajar con VIANKA GOLD MINING?'],
            [
                'knowledge_category_id' => $knowledgeCategories['Afiliaciones']->id,
                'answer' => 'Para afiliarte se registran tus datos, ciudad y experiencia comercial. Luego el Supervisor Comercial valida el siguiente paso.',
                'keywords' => 'afiliarme registrarme trabajar vender supervisor comercial',
                'priority' => 9,
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Anillo de oro demo'],
            [
                'description' => 'Producto de prueba para validar respuestas sobre joyas, precios y disponibilidad.',
                'price' => 1500,
                'is_active' => true,
            ]
        );

        Promotion::updateOrCreate(
            ['name' => 'Promocion de bienvenida'],
            [
                'description' => 'Promocion demo para nuevas consultas registradas en el CRM.',
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->addMonth()->toDateString(),
                'is_active' => true,
            ]
        );

        Raffle::updateOrCreate(
            ['name' => 'Sorteo demo VIANKA'],
            [
                'description' => 'Sorteo de prueba para validar el modulo de conocimiento.',
                'prizes' => 'Premio demo: joya promocional.',
                'raffle_date' => now()->addWeeks(2)->toDateString(),
                'rules' => 'Reglamento de prueba pendiente de aprobacion legal.',
                'is_active' => true,
            ]
        );

        $intentions = [
            ['Compra de joya', 'compra-de-joya', '#d8b44c', 'cart', 100, Intention::ACTION_RESPOND_AI, 'Ventas', 0.70, 'comprar,quiero una joya,catalogo,catalogo joyas,joya,anillo,collar,pulsera'],
            ['Consulta de precios', 'consulta-de-precios', '#0d6efd', 'tag', 95, Intention::ACTION_RESPOND_AI, 'Ventas', 0.65, 'precio,cuanto cuesta,valor,costo,pagar,reserva'],
            ['Sorteos', 'sorteos', '#6610f2', 'gift', 90, Intention::ACTION_RESPOND_AI, null, 0.60, 'sorteo,premio,ticket,ganador,rifa'],
            ['Promociones', 'promociones', '#fd7e14', 'badge-percent', 88, Intention::ACTION_RESPOND_AI, null, 0.60, 'promocion,promociones,descuento,oferta,beneficio'],
            ['Afiliacion', 'afiliacion', '#20c997', 'user-plus', 86, Intention::ACTION_RESPOND_AI, 'Supervisor Comercial', 0.65, 'afiliarme,afiliacion,registrarme,registro,inscripcion'],
            ['Trabajo', 'trabajo', '#198754', 'briefcase', 84, Intention::ACTION_RESPOND_AI, 'Supervisor Comercial', 0.65, 'trabajar,vender,empleo,asesor,trabajo'],
            ['Inversion', 'inversion', '#ffc107', 'trending-up', 82, Intention::ACTION_DERIVE, 'Gerencia Comercial', 0.60, 'invertir,inversion,accion,acciones,rentabilidad,ganancia'],
            ['Reclamo', 'reclamo', '#dc3545', 'alert-triangle', 80, Intention::ACTION_DERIVE, 'Soporte', 0.60, 'reclamo,problema,no recibi,fallo,falla,malo,queja'],
            ['Garantia', 'garantia', '#6f42c1', 'shield', 78, Intention::ACTION_DERIVE, 'Soporte', 0.60, 'garantia,cambio,devolucion,reparacion'],
            ['Estado de pedido', 'estado-de-pedido', '#0dcaf0', 'truck', 76, Intention::ACTION_RESPOND_AI, 'Soporte', 0.60, 'pedido,estado,seguimiento,envio,entrega,donde esta'],
            ['Pagos', 'pagos', '#198754', 'credit-card', 74, Intention::ACTION_RESPOND_AI, 'Ventas', 0.60, 'pago,pagar,transferencia,qr,deposito,tarjeta'],
            ['Facturacion', 'facturacion', '#6c757d', 'receipt', 72, Intention::ACTION_RESPOND_AI, 'Soporte', 0.60, 'factura,facturacion,nit,recibo'],
            ['Ubicacion', 'ubicacion', '#0d6efd', 'map-pin', 70, Intention::ACTION_RESPOND_AI, null, 0.60, 'ubicacion,direccion,donde estan,contacto,telefono,horario'],
            ['Hablar con asesor', 'hablar-con-asesor', '#212529', 'headphones', 68, Intention::ACTION_DERIVE, 'Ventas', 0.60, 'asesor,humano,persona,atencion,hablar,contactarme'],
            ['Otros', 'otros', '#adb5bd', 'circle-help', 1, Intention::ACTION_RESPOND_AI, null, 0.40, 'consulta,informacion,ayuda'],
        ];

        $seededIntentions = collect($intentions)->mapWithKeys(function (array $row) use ($areas) {
            [$name, $slug, $color, $icon, $priority, $action, $areaName, $confidence, $keywords] = $row;

            return [$slug => Intention::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => "Intencion para clasificar consultas sobre {$name}.",
                    'color' => $color,
                    'icon' => $icon,
                    'priority' => $priority,
                    'is_active' => true,
                    'default_action' => $action,
                    'derivation_area_id' => $areaName ? $areas[$areaName]->id : null,
                    'minimum_confidence' => $confidence,
                    'specific_prompt' => null,
                    'keywords' => $keywords,
                ]
            )];
        });

        KnowledgeFaq::where('question', 'Como puedo afiliarme para trabajar con VIANKA GOLD MINING?')
            ->first()
            ?->intentions()
            ->syncWithoutDetaching([$seededIntentions['afiliacion']->id, $seededIntentions['trabajo']->id]);

        KnowledgeFaq::where('question', 'Donde esta ubicada VIANKA GOLD MINING?')
            ->first()
            ?->intentions()
            ->syncWithoutDetaching([$seededIntentions['ubicacion']->id]);

        Product::where('name', 'Anillo de oro demo')
            ->first()
            ?->intentions()
            ->syncWithoutDetaching([$seededIntentions['compra-de-joya']->id, $seededIntentions['consulta-de-precios']->id]);

        Promotion::where('name', 'Promocion de bienvenida')
            ->first()
            ?->intentions()
            ->syncWithoutDetaching([$seededIntentions['promociones']->id]);

        Raffle::where('name', 'Sorteo demo VIANKA')
            ->first()
            ?->intentions()
            ->syncWithoutDetaching([$seededIntentions['sorteos']->id]);

        AiPromptTemplate::updateOrCreate(
            ['type' => 'respuesta_cliente', 'name' => 'Respuesta cliente base'],
            [
                'content' => 'Eres un asesor virtual de VIANKA GOLD MINING. Responde de forma clara, amable y comercial. Usa únicamente la información proporcionada en la base de conocimiento. Si no tienes información suficiente, no inventes. Indica que derivarás la consulta al área correspondiente. No prometas rentabilidad, ganancias garantizadas ni beneficios no documentados.',
                'is_active' => true,
            ]
        );

        AiPromptTemplate::updateOrCreate(
            ['type' => 'clasificacion_intencion', 'name' => 'Clasificacion intencion base'],
            [
                'content' => 'Clasifica el mensaje del cliente según las intenciones disponibles. Devuelve JSON válido con: intention_slug, confidence, action, derivation_area_slug, reason.',
                'is_active' => true,
            ]
        );

        AiPromptTemplate::updateOrCreate(
            ['type' => 'derivacion', 'name' => 'Derivacion base'],
            [
                'content' => 'Cuando la consulta requiera atención humana, genera un mensaje breve indicando el área a la que será derivada.',
                'is_active' => true,
            ]
        );

        AiPromptTemplate::updateOrCreate(
            ['type' => 'seguridad', 'name' => 'Seguridad comercial base'],
            [
                'content' => 'No inventes precios, premios, fechas, condiciones, garantías ni rentabilidad. Si falta información, deriva.',
                'is_active' => true,
            ]
        );

        AiProviderSetting::updateOrCreate(
            ['provider' => 'openai', 'name' => 'OpenAI GPT'],
            [
                'model' => 'gpt-4o-mini',
                'endpoint' => 'https://api.openai.com/v1/chat/completions',
                'temperature' => 0.30,
                'max_tokens' => 800,
                'timeout_seconds' => 30,
                'is_active' => false,
                'is_default' => true,
                'notes' => 'Configura la API key para activar OpenAI.',
            ]
        );

        AiProviderSetting::updateOrCreate(
            ['provider' => 'deepseek', 'name' => 'DeepSeek Chat'],
            [
                'model' => 'deepseek-chat',
                'endpoint' => 'https://api.deepseek.com/chat/completions',
                'temperature' => 0.30,
                'max_tokens' => 800,
                'timeout_seconds' => 30,
                'is_active' => false,
                'is_default' => false,
                'notes' => 'Configura la API key para activar DeepSeek.',
            ]
        );
    }
}
