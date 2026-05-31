<?php

namespace App\Services;

use App\Contracts\KnowledgeProviderInterface;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeFaq;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Raffle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class KnowledgeBaseService implements KnowledgeProviderInterface
{
    /**
     * @return Collection<int, KnowledgeFaq>
     */
    public function buscarFaqs(string $question): Collection
    {
        $tokens = $this->tokens($question);

        return KnowledgeFaq::with(['category', 'intentions'])
            ->where('is_active', true)
            ->get()
            ->map(fn (KnowledgeFaq $faq) => $this->withScore($faq, $tokens, [
                $faq->question,
                $faq->answer,
                $faq->keywords,
                $faq->category?->name,
                $faq->intentions->pluck('name')->implode(' '),
            ]))
            ->filter(fn (KnowledgeFaq $faq) => $faq->score > 0)
            ->sortByDesc(fn (KnowledgeFaq $faq) => ($faq->score * 1000) + $faq->priority)
            ->take(5)
            ->values();
    }

    /**
     * @return Collection<int, Product>
     */
    public function buscarProductos(string $question): Collection
    {
        $tokens = $this->tokens($question);

        return Product::with('intentions')->where('is_active', true)
            ->get()
            ->map(fn (Product $product) => $this->withScore($product, $tokens, [
                $product->name,
                $product->description,
                $product->intentions->pluck('name')->implode(' '),
            ]))
            ->filter(fn (Product $product) => $product->score > 0)
            ->sortByDesc('score')
            ->take(5)
            ->values();
    }

    /**
     * @return Collection<int, KnowledgeDocument>
     */
    public function buscarDocumentos(string $question): Collection
    {
        $tokens = $this->tokens($question);

        return KnowledgeDocument::with(['category', 'intentions'])
            ->where('is_active', true)
            ->get()
            ->map(fn (KnowledgeDocument $document) => $this->withScore($document, $tokens, [
                $document->title,
                $document->description,
                $document->original_filename,
                $document->category?->name,
                $document->intentions->pluck('name')->implode(' '),
            ]))
            ->filter(fn (KnowledgeDocument $document) => $document->score > 0)
            ->sortByDesc('score')
            ->take(5)
            ->values();
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function buscarPromociones(string $question): Collection
    {
        $tokens = $this->tokens($question);

        return Promotion::with('intentions')->where('is_active', true)
            ->get()
            ->map(fn (Promotion $promotion) => $this->withScore($promotion, $tokens, [
                $promotion->name,
                $promotion->description,
                $promotion->intentions->pluck('name')->implode(' '),
            ]))
            ->filter(fn (Promotion $promotion) => $promotion->score > 0)
            ->sortByDesc('score')
            ->take(3)
            ->values();
    }

    /**
     * @return Collection<int, Raffle>
     */
    public function buscarSorteos(string $question): Collection
    {
        $tokens = $this->tokens($question);

        return Raffle::with('intentions')->where('is_active', true)
            ->get()
            ->map(fn (Raffle $raffle) => $this->withScore($raffle, $tokens, [
                $raffle->name,
                $raffle->description,
                $raffle->prizes,
                $raffle->rules,
                $raffle->intentions->pluck('name')->implode(' '),
            ]))
            ->filter(fn (Raffle $raffle) => $raffle->score > 0)
            ->sortByDesc('score')
            ->take(3)
            ->values();
    }

    /**
     * @return array{answer:string, faqs:Collection, products:Collection, documents:Collection, promotions:Collection, raffles:Collection, sources:array<int, array<string, mixed>>, intention:?Intention, confidence:float, recommended_action:string, derivation_area:?DerivationArea}
     */
    public function generarRespuestaLocal(string $question): array
    {
        $detected = $this->detectarIntencionBasica($question);
        $intention = $detected['intention'];
        $confidence = $detected['confidence'];
        $recommendedAction = $detected['recommended_action'];
        $derivationArea = $detected['derivation_area'];

        $faqs = $this->buscarFaqs($question);
        $products = $this->buscarProductos($question);
        $documents = $this->buscarDocumentos($question);
        $promotions = $this->buscarPromociones($question);
        $raffles = $this->buscarSorteos($question);

        if ($intention) {
            $byIntention = $this->buscarPorIntencion($intention->id);
            $faqs = $this->mergeById($byIntention['faqs'], $faqs);
            $products = $this->mergeById($byIntention['products'], $products);
            $documents = $this->mergeById($byIntention['documents'], $documents);
            $promotions = $this->mergeById($byIntention['promotions'], $promotions);
            $raffles = $this->mergeById($byIntention['raffles'], $raffles);
        }

        $parts = [];
        $sources = [];

        foreach ($faqs as $faq) {
            $parts[] = "FAQ: {$faq->question}\n{$faq->answer}";
            $sources[] = ['type' => 'faq', 'id' => $faq->id, 'title' => Str::limit($faq->question, 80), 'category' => $faq->category?->name];
        }

        foreach ($products as $product) {
            $price = $product->price ? ' Precio: Bs. '.$product->price.'.' : '';
            $parts[] = "Producto: {$product->name}. {$product->description}{$price}";
            $sources[] = ['type' => 'producto', 'id' => $product->id, 'title' => $product->name];
        }

        foreach ($documents as $document) {
            $parts[] = "Documento: {$document->title}. {$document->description}";
            $sources[] = ['type' => 'documento', 'id' => $document->id, 'title' => $document->title, 'category' => $document->category?->name, 'file' => $document->original_filename];
        }

        foreach ($promotions as $promotion) {
            $parts[] = "Promocion: {$promotion->name}. {$promotion->description}";
            $sources[] = ['type' => 'promocion', 'id' => $promotion->id, 'title' => $promotion->name];
        }

        foreach ($raffles as $raffle) {
            $parts[] = "Sorteo: {$raffle->name}. Premios: {$raffle->prizes}. Reglamento: {$raffle->rules}";
            $sources[] = ['type' => 'sorteo', 'id' => $raffle->id, 'title' => $raffle->name];
        }

        $header = $intention
            ? "Intencion detectada: {$intention->name} ({$confidence}). Accion recomendada: {$recommendedAction}."
            : "Intencion detectada: Sin clasificar ({$confidence}). Accion recomendada: {$recommendedAction}.";

        if ($derivationArea) {
            $header .= " Area sugerida: {$derivationArea->name}.";
        }

        $answer = $parts
            ? "{$header}\n\nRespuesta local simulada basada en la base de conocimiento:\n\n".implode("\n\n", array_slice($parts, 0, 8))
            : "{$header}\n\nNo encontre informacion suficiente en la base de conocimiento activa. Agrega FAQs, productos o documentos relacionados para mejorar la respuesta.";

        return compact('answer', 'faqs', 'products', 'documents', 'promotions', 'raffles', 'sources', 'intention', 'confidence', 'recommendedAction', 'derivationArea') + [
            'recommended_action' => $recommendedAction,
            'derivation_area' => $derivationArea,
        ];
    }

    /**
     * @return array{intention:?Intention, confidence:float, recommended_action:string, derivation_area:?DerivationArea}
     */
    public function detectarIntencionBasica(string $message): array
    {
        $text = Str::lower(Str::ascii($message));
        $intentions = Intention::with('derivationArea')
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        $scores = [];

        foreach ($intentions as $intention) {
            $keywords = collect(explode(',', (string) $intention->keywords))
                ->map(fn (string $keyword) => trim(Str::lower(Str::ascii($keyword))))
                ->filter();
            $matches = $keywords->filter(fn (string $keyword) => str_contains($text, $keyword))->count();

            if ($matches > 0) {
                $scores[] = ['intention' => $intention, 'matches' => $matches];
            }
        }

        $manual = [
            'consulta-de-precios' => ['precio', 'cuanto cuesta', 'valor'],
            'compra-de-joya' => ['comprar', 'quiero una joya', 'catalogo'],
            'inversion' => ['invertir', 'accion', 'acciones', 'rentabilidad'],
            'reclamo' => ['reclamo', 'problema', 'no recibi', 'fallo'],
            'garantia' => ['garantia', 'cambio', 'devolucion'],
            'sorteos' => ['sorteo', 'premio', 'ticket'],
            'trabajo' => ['trabajar', 'vender'],
            'afiliacion' => ['afiliarme', 'registrarme', 'afiliacion'],
        ];

        foreach ($manual as $slug => $keywords) {
            $matches = collect($keywords)->filter(fn (string $keyword) => str_contains($text, $keyword))->count();
            if ($matches > 0 && $intentions->firstWhere('slug', $slug)) {
                $scores[] = ['intention' => $intentions->firstWhere('slug', $slug), 'matches' => $matches + 1];
            }
        }

        $winner = collect($scores)
            ->sortByDesc(fn (array $score) => ($score['matches'] * 1000) + $score['intention']->priority)
            ->first();

        $intention = $winner['intention'] ?? $intentions->firstWhere('slug', 'otros');
        $confidence = $winner ? min(0.98, 0.50 + ($winner['matches'] * 0.15)) : 0.40;

        [$action, $area] = $this->actionForIntention($intention, $text, $confidence);

        return [
            'intention' => $intention,
            'confidence' => round($confidence, 2),
            'recommended_action' => $action,
            'derivation_area' => $area,
        ];
    }

    /**
     * @return array{faqs:Collection, products:Collection, documents:Collection, promotions:Collection, raffles:Collection}
     */
    public function buscarPorIntencion(int $intentionId): array
    {
        return [
            'faqs' => KnowledgeFaq::with(['category', 'intentions'])->where('is_active', true)->whereHas('intentions', fn ($query) => $query->whereKey($intentionId))->orderByDesc('priority')->limit(5)->get(),
            'products' => Product::with('intentions')->where('is_active', true)->whereHas('intentions', fn ($query) => $query->whereKey($intentionId))->limit(5)->get(),
            'documents' => KnowledgeDocument::with(['category', 'intentions'])->where('is_active', true)->whereHas('intentions', fn ($query) => $query->whereKey($intentionId))->latest('uploaded_at')->limit(5)->get(),
            'promotions' => Promotion::with('intentions')->where('is_active', true)->whereHas('intentions', fn ($query) => $query->whereKey($intentionId))->limit(3)->get(),
            'raffles' => Raffle::with('intentions')->where('is_active', true)->whereHas('intentions', fn ($query) => $query->whereKey($intentionId))->limit(3)->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function search(string $question): array
    {
        return $this->generarRespuestaLocal($question);
    }

    /**
     * @param array<int, string|null> $fields
     */
    private function withScore(object $model, array $tokens, array $fields): object
    {
        $haystack = Str::lower(Str::ascii(implode(' ', array_filter($fields))));
        $score = 0;

        foreach ($tokens as $token) {
            if (str_contains($haystack, $token)) {
                $score++;
            }
        }

        $model->score = $score;

        return $model;
    }

    private function actionForIntention(?Intention $intention, string $text, float $confidence): array
    {
        if (! $intention) {
            return [Intention::ACTION_RESPOND_AI, null];
        }

        $area = $intention->derivationArea;
        $action = $intention->default_action;

        if (in_array($intention->slug, ['inversion', 'reclamo', 'garantia'], true)) {
            return [Intention::ACTION_DERIVE, $area];
        }

        if ($intention->slug === 'compra-de-joya' && $confidence >= 0.75) {
            return [Intention::ACTION_RESPOND_AND_DERIVE, $area ?: DerivationArea::where('name', 'Ventas')->first()];
        }

        if (in_array($intention->slug, ['trabajo', 'afiliacion'], true) && $this->containsAny($text, ['registrarme', 'afiliarme', 'afiliacion', 'registro'])) {
            return [Intention::ACTION_RESPOND_AND_DERIVE, $area ?: DerivationArea::where('name', 'Supervisor Comercial')->first()];
        }

        if ($action !== Intention::ACTION_RESPOND_AI && ! $area) {
            $area = $intention->derivationArea;
        }

        return [$action, $area];
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function mergeById(Collection $primary, Collection $secondary): Collection
    {
        return $primary->merge($secondary)->unique('id')->values();
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $question): array
    {
        return collect(preg_split('/\s+/', Str::lower(Str::ascii($question))) ?: [])
            ->map(fn (string $token) => trim($token, " \t\n\r\0\x0B.,;:!?()[]{}\"'"))
            ->filter(fn (string $token) => strlen($token) >= 3)
            ->unique()
            ->values()
            ->all();
    }
}
