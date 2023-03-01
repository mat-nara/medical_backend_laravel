<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\HematologieController;
use App\Http\Controllers\Api\BiochimieController;
use App\Http\Controllers\Api\AutreBiologieController;
use App\Http\Controllers\Api\UrineController;
use App\Http\Controllers\Api\AutrePrelevementBiologieController;
use App\Http\Controllers\Api\AntibiogrammeController;
use App\Http\Controllers\Api\ImagerieController;
use App\Http\Controllers\Api\RadiologieController;
use App\Http\Controllers\Api\EchographieController;
use App\Http\Controllers\Api\AutreImagerieController;
use App\Http\Controllers\Api\ExamenFonctionnelController;
use App\Http\Controllers\Api\EvolutionController;
use App\Http\Controllers\Api\TraitementController;






/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware'=> ['cors']], function(){

    Route::post('login',                            [AuthenticationController::class, 'login']);
    Route::get('logout',                            [AuthenticationController::class, 'logout'])             ->middleware('auth:sanctum');
    Route::get('authenticated-user',                [AuthenticationController::class, 'authenticated_user']) ->middleware('auth:sanctum');
    Route::post('send-password-reset-link-email',   [AuthenticationController::class, 'sendPasswordResetLinkEmail']);
    Route::post('reset-password',                   [AuthenticationController::class, 'resetPassword'])->name('password.reset');

    Route::get('users',           [UserController::class, 'index'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::post('users',          [UserController::class, 'store'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::get('users/{user}',    [UserController::class, 'show'])      ->middleware(['auth:sanctum']);
    Route::put('users/{user}',    [UserController::class, 'update'])    ->middleware(['auth:sanctum']);
    Route::delete('users/{user}', [UserController::class, 'destroy'])   ->middleware(['auth:sanctum', 'ability:admin']);

    Route::get('patients',              [PatientController::class, 'index'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::post('patients',             [PatientController::class, 'store'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::get('patients/{patient}',    [PatientController::class, 'show'])      ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/observation', [PatientController::class, 'show_with_observation'])->middleware(['auth:sanctum']);
    Route::put('patients/{patient}',    [PatientController::class, 'update'])    ->middleware(['auth:sanctum', 'ability:admin']);
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])   ->middleware(['auth:sanctum', 'ability:admin']);

    Route::get('patients/{patient}/hematologies',                   [HematologieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/hematologies',                  [HematologieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/hematologies/{hematologie}',     [HematologieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/hematologies/{hematologie}',     [HematologieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/hematologies/{hematologie}',  [HematologieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/biochimies',                 [BiochimieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/biochimies',                [BiochimieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/biochimies/{biochimie}',     [BiochimieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/biochimies/{biochimie}',     [BiochimieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/biochimies/{biochimie}',  [BiochimieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/autres-biologies',                       [AutreBiologieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/autres-biologies',                      [AutreBiologieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/autres-biologies/{autre_biologie}',      [AutreBiologieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/autres-biologies/{autre_biologie}',      [AutreBiologieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/autres-biologies/{autre_biologie}',   [AutreBiologieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/urines',             [UrineController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/urines',            [UrineController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/urines/{urine}',     [UrineController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/urines/{urine}',     [UrineController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/urines/{urine}',  [UrineController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/autres-prelevements-biologies',                          [AutrePrelevementBiologieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/autres-prelevements-biologies',                         [AutrePrelevementBiologieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/autres-prelevements-biologies/{autre_prelevement}',      [AutrePrelevementBiologieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/autres-prelevements-biologies/{autre_prelevement}',      [AutrePrelevementBiologieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/autres-prelevements-biologies/{autre_prelevement}',   [AutrePrelevementBiologieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/antibiogrammes',                     [AntibiogrammeController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/antibiogrammes',                    [AntibiogrammeController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/antibiogrammes/{antibiogramme}',     [AntibiogrammeController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/antibiogrammes/{antibiogramme}',     [AntibiogrammeController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/antibiogrammes/{antibiogramme}',  [AntibiogrammeController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::post('imageries/upload',                 [ImagerieController::class, 'upload']) ->middleware(['auth:sanctum']);
    Route::delete('imageries/delete/{image_name}',  [ImagerieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/radiologies',                  [RadiologieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/radiologies',                 [RadiologieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/radiologies/{radiologie}',     [RadiologieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/radiologies/{radiologie}',     [RadiologieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/radiologies/{radiologie}',  [RadiologieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/echographies',                  [EchographieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/echographies',                 [EchographieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/echographies/{echographie}',    [EchographieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/echographies/{echographie}',    [EchographieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/echographies/{echographie}', [EchographieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/autres-imageries',                       [AutreImagerieController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/autres-imageries',                      [AutreImagerieController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/autres-imageries/{autre_imagerie}',      [AutreImagerieController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/autres-imageries/{autre_imagerie}',      [AutreImagerieController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/autres-imageries/{autre_imagerie}',   [AutreImagerieController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/examens-fonctionnels',                           [ExamenFonctionnelController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/examens-fonctionnels',                          [ExamenFonctionnelController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/examens-fonctionnels/{examen_fonctionnel}',      [ExamenFonctionnelController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/examens-fonctionnels/{examen_fonctionnel}',      [ExamenFonctionnelController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/examens-fonctionnels/{examen_fonctionnel}',   [ExamenFonctionnelController::class, 'destroy'])->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/evolutions',                  [EvolutionController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/evolutions',                 [EvolutionController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/evolutions/{evolution}',      [EvolutionController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/evolutions/{evolution}',      [EvolutionController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/evolutions/{evolution}',   [EvolutionController::class, 'destroy'])->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/evolutions/{date}/by_date',   [EvolutionController::class, 'find_by_date']) ->middleware(['auth:sanctum']);

    Route::get('patients/{patient}/traitements',                    [TraitementController::class, 'index']) ->middleware(['auth:sanctum']);
    Route::post('patients/{patient}/traitements',                   [TraitementController::class, 'store']) ->middleware(['auth:sanctum']);
    Route::get('patients/{patient}/traitements/{traitement}',       [TraitementController::class, 'show'])  ->middleware(['auth:sanctum']);
    Route::put('patients/{patient}/traitements/{traitement}',       [TraitementController::class, 'update'])->middleware(['auth:sanctum']);
    Route::delete('patients/{patient}/traitements/{traitement}',    [TraitementController::class, 'destroy'])->middleware(['auth:sanctum']);
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



#Route::apiResource('roles', RoleController::class)->except(['create', 'edit'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);

