<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Judehashane\Seatbelt\Configurations\StrictModels;

class StrictModelsTestAuthor extends Model
{
    public $timestamps = false;

    protected $table = 'strict_models_test_authors';

    protected $guarded = [];

    public function books(): HasMany
    {
        return $this->hasMany(StrictModelsTestBook::class, 'author_id');
    }
}

class StrictModelsTestBook extends Model
{
    public $timestamps = false;

    protected $table = 'strict_models_test_books';

    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(StrictModelsTestAuthor::class, 'author_id');
    }
}

beforeEach(function (): void {
    Schema::create('strict_models_test_authors', function (Blueprint $table): void {
        $table->id();
    });

    Schema::create('strict_models_test_books', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('author_id')->constrained('strict_models_test_authors');
    });

    StrictModelsTestAuthor::query()->insert([['id' => 1], ['id' => 2]]);
    StrictModelsTestBook::query()->insert([
        ['author_id' => 1],
        ['author_id' => 2],
    ]);
});

afterEach(function (): void {
    Model::shouldBeStrict(false);
    Schema::dropIfExists('strict_models_test_authors');
    Schema::dropIfExists('strict_models_test_books');
});

it('prevents lazy loading when enabled', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new StrictModels($app, $config))->apply();

    $authors = StrictModelsTestAuthor::query()->get();

    $authors[0]->books; // lazy access — should throw instead of querying
})->throws(LazyLoadingViolationException::class);
