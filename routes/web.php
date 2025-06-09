<?php

use App\Http\Controllers\Admin\CompetencyController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ProficiencyLevelController;
use App\Http\Controllers\Admin\QuarterController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Plugins\AssessmentToolController;
use App\Models\Chat;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['auth'])->group(function(){
    Route::get('/', function () {
        // return Inertia::render('Home');
        return redirect()->route('client.plugins.assessment-tool');
    })->name('client.home');

    Route::prefix('plugins')->group(function(){
        Route::get('assessment-tool', [AssessmentToolController::class, 'index'])->name('client.plugins.assessment-tool');
    });

    // Prompt
    Route::post('prompt/{id?}', [ChatController::class, 'store'])->name('client.plugins.chats.store');

    Route::prefix('admin')->group(function(){
        Route::get('/', function(){
            return Inertia::render('Admin/Home');
        })->name('admin.home');
        Route::prefix('assessment-tool')->group(function(){
            // Grades
            Route::get('grades', [GradeController::class, 'index'])->name('admin.grades.index');
            Route::post('grades', [GradeController::class, 'store'])->name('admin.grades.store');
            Route::get('grades/all', [GradeController::class, 'all'])->name('admin.grades.all');

            // Subjects
            Route::get('subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
            Route::post('subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
            Route::get('subjects/all', [SubjectController::class, 'all'])->name('admin.subjects.all');

            // Quarters
            Route::get('quarters', [QuarterController::class, 'index'])->name('admin.quarters.index');
            Route::post('quarters', [QuarterController::class, 'store'])->name('admin.quarters.store');
            Route::get('quarters/all', [QuarterController::class, 'all'])->name('admin.quarters.all');

            // Contents
            Route::get('contents', [ContentController::class, 'index'])->name('admin.contents.index');
            Route::post('contents', [ContentController::class, 'store'])->name('admin.contents.store');
            Route::get('contents/all', [ContentController::class, 'all'])->name('admin.contents.all');

            // Proficiency Levels
            Route::get('proficiency-levels', [ProficiencyLevelController::class, 'index'])->name('admin.proficiency-levels.index');
            Route::post('proficiency-levels', [ProficiencyLevelController::class, 'store'])->name('admin.proficiency-levels.store');
            Route::get('proficiency-levels/all', [ProficiencyLevelController::class, 'all'])->name('admin.proficiency-levels.all');

            // Languages
            Route::get('languages', [LanguageController::class, 'index'])->name('admin.languages.index');
            Route::post('languages', [LanguageController::class, 'store'])->name('admin.languages.store');
            Route::get('languages/all', [LanguageController::class, 'all'])->name('admin.languages.all');

            // Competencies
            Route::get('competencies/all', [CompetencyController::class, 'all'])->name('admin.competencies.all');

            

        });
    });
});

