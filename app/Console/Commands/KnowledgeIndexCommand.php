<?php

namespace App\Console\Commands;

use App\Services\RAG\KnowledgeIndexerService;
use Illuminate\Console\Command;

class KnowledgeIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'knowledge:index {scope=all : all|faqs|documents|products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera embeddings y chunks de la base de conocimiento';

    /**
     * Execute the console command.
     */
    public function handle(KnowledgeIndexerService $indexer): int
    {
        $scope = (string) $this->argument('scope');
        $this->info("Indexando conocimiento: {$scope}");

        $count = $indexer->reindex($scope, function (string $type, int $id, int $count) {
            $this->line("{$count}. {$type} #{$id}");
        });

        $this->info("Embeddings creados: {$count}");

        return self::SUCCESS;
    }
}
