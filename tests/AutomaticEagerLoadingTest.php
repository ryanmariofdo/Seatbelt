<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Judehashane\Seatbelt\Configurations\AutomaticEagerLoading;

class EagerLoadingTestAuthor extends Model
{
    public $timestamps = false;

    protected $table = 'eager_loading_test_authors';

    protected $guarded = [];

    public function books(): HasMany
    {
        return $this->hasMany(EagerLoadingTestBook::class, 'author_id');
    }
}

class EagerLoadingTestBook extends Model
{
    public $timestamps = false;

    protected $table = 'eager_loading_test_books';

    protected $guarded = [];
}

beforeEach(function (): void {
    Schema::create('eager_loading_test_authors', function (Blueprint $table): void {
        $table->id();
    });

    Schema::create('eager_loading_test_books', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('author_id');
    });

    EagerLoadingTestAuthor::query()->insert([['id' => 1], ['id' => 2]]);
    EagerLoadingTestBook::query()->insert([
        ['author_id' => 1],
        ['author_id' => 2],
    ]);
});

afterEach(function (): void {
    Model::automaticallyEagerLoadRelationships(false);
    Schema::dropIfExists('eager_loading_test_books');
    Schema::dropIfExists('eager_loading_test_authors');
});

it('auto eager loads a relation across a collection once accessed on any one model', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new AutomaticEagerLoading($app, $config))->apply();

    $authors = EagerLoadingTestAuthor::query()->get();

    DB::enableQueryLog();

    $authors[0]->books; // lazy access on the first model in the collection
    $queriesAfterFirstAccess = count(DB::getQueryLog());

    $authors[1]->books; // second model — should be a cache hit, not a new query
    $queriesAfterSecondAccess = count(DB::getQueryLog());

    expect($queriesAfterFirstAccess)->toBe(1)
        ->and($queriesAfterSecondAccess)->toBe(1);
});
