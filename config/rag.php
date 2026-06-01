<?php

return [
    'engine' => env('RAG_ENGINE', 'mysql'),
    'chunk_size' => (int) env('RAG_CHUNK_SIZE', 900),
    'chunk_overlap' => (int) env('RAG_CHUNK_OVERLAP', 150),
    'embedding_model' => env('RAG_EMBEDDING_MODEL', 'local-hash-v1'),
    'embedding_dimensions' => (int) env('RAG_EMBEDDING_DIMENSIONS', 128),
    'semantic_limit' => (int) env('RAG_SEMANTIC_LIMIT', 8),
    'minimum_score' => (float) env('RAG_MINIMUM_SCORE', 0.10),
    'postgres_connection' => env('RAG_POSTGRES_CONNECTION', 'pgsql'),
];
