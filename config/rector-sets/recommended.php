<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php85\Rector\Property\AddOverrideAttributeToOverriddenPropertiesRector;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;
use RectorPest\Set\PestLevelSetList;
use RectorPest\Set\PestSetList;

/**
 * Seatbelt's curated Laravel + Pest rule selection. Referenced live from
 * vendor/ via SeatbeltSetList::RECOMMENDED — never copy this file, import it.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(LaravelLevelSetList::UP_TO_LARAVEL_130);
    $rectorConfig->import(LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL);
    $rectorConfig->import(LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL);
    $rectorConfig->import(LaravelSetList::LARAVEL_CODE_QUALITY);
    $rectorConfig->import(LaravelSetList::LARAVEL_COLLECTION);
    $rectorConfig->import(LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME);
    $rectorConfig->import(LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER);
    $rectorConfig->import(LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES);
    $rectorConfig->import(LaravelSetList::LARAVEL_FACTORIES);
    $rectorConfig->import(LaravelSetList::LARAVEL_IF_HELPERS);
    $rectorConfig->import(LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES);

    $rectorConfig->import(PestLevelSetList::UP_TO_PEST_40);
    $rectorConfig->import(PestSetList::PEST_CODE_QUALITY);
    $rectorConfig->import(PestSetList::PEST_CHAIN);

    $rectorConfig->rule(AddGenericReturnTypeToRelationsRector::class);

    $rectorConfig->importNames(true, true);
    $rectorConfig->importShortClasses(true);
    $rectorConfig->removeUnusedImports(true);

    $rectorConfig->skip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        MakeInheritedMethodVisibilitySameAsParentRector::class,
        AddOverrideAttributeToOverriddenPropertiesRector::class,
    ]);
};
