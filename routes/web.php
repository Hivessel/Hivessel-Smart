<?php

use App\Http\Controllers\Admin\CompetencyController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ProficiencyLevelController;
use App\Http\Controllers\Admin\QuarterController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Plugins\AssessmentToolController;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;


Route::post('remote-login', [AuthController::class, 'remoteLogin'])->name('remote-login');

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
            Route::get('grades/ajax', [GradeController::class, 'grades'])->name('admin.grades.ajax');
            Route::delete('grades', [GradeController::class, 'destroy'])->name('admin.grades.destroy');

            // Subjects
            Route::get('subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
            Route::post('subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
            Route::get('subjects/all', [SubjectController::class, 'all'])->name('admin.subjects.all');
            Route::get('subjects/ajax', [SubjectController::class, 'subjects'])->name('admin.subjects.ajax');
            Route::delete('subjects', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

            // Quarters
            Route::get('quarters', [QuarterController::class, 'index'])->name('admin.quarters.index');
            Route::post('quarters', [QuarterController::class, 'store'])->name('admin.quarters.store');
            Route::get('quarters/all', [QuarterController::class, 'all'])->name('admin.quarters.all');
            Route::get('quarters/ajax', [QuarterController::class, 'quarters'])->name('admin.quarters.ajax');
            Route::delete('quarters', [QuarterController::class, 'destroy'])->name('admin.quarters.destroy');

            // Contents
            Route::get('contents', [ContentController::class, 'index'])->name('admin.contents.index');
            Route::post('contents', [ContentController::class, 'store'])->name('admin.contents.store');
            Route::get('contents/all', [ContentController::class, 'all'])->name('admin.contents.all');
            Route::get('contents/ajax', [ContentController::class, 'contents'])->name('admin.contents.ajax');
            Route::delete('contents', [ContentController::class, 'destroy'])->name('admin.contents.destroy');

            // Proficiency Levels
            Route::get('proficiency-levels', [ProficiencyLevelController::class, 'index'])->name('admin.proficiency-levels.index');
            Route::post('proficiency-levels', [ProficiencyLevelController::class, 'store'])->name('admin.proficiency-levels.store');
            Route::get('proficiency-levels/all', [ProficiencyLevelController::class, 'all'])->name('admin.proficiency-levels.all');
            Route::get('proficiency-levels/ajax', [ProficiencyLevelController::class, 'proficiencyLevels'])->name('admin.proficiency-levels.ajax');
            Route::delete('proficiency-levels', [ProficiencyLevelController::class, 'destroy'])->name('admin.proficiency-levels.destroy');

            // Languages
            Route::get('languages', [LanguageController::class, 'index'])->name('admin.languages.index');
            Route::post('languages', [LanguageController::class, 'store'])->name('admin.languages.store');
            Route::get('languages/all', [LanguageController::class, 'all'])->name('admin.languages.all');
            Route::get('languages/ajax', [LanguageController::class, 'languages'])->name('admin.languages.ajax');
            Route::delete('languages', [LanguageController::class, 'destroy'])->name('admin.languages.destroy');

            // Competencies
            Route::get('competencies/all', [CompetencyController::class, 'all'])->name('admin.competencies.all');

            

        });
    });
});
