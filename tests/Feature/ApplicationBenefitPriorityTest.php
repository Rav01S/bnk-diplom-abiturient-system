<?php

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('military and svo children benefits receive ranking priority', function (string $benefitType) {
    $application = new Application([
        'is_benefit' => true,
        'benefit_type' => $benefitType,
    ]);

    expect($application->hasPriorityBenefit())->toBeTrue();
})->with([
    'military injury disability' => Application::BenefitMilitaryInjuryDisability,
    'military disease disability' => Application::BenefitMilitaryDiseaseDisability,
    'combat veterans' => Application::BenefitCombatVeterans,
    'svo children' => Application::BenefitSvoChildren,
    'legacy svo' => 'svo',
]);

test('non military benefits do not receive ranking priority', function (string $benefitType) {
    $application = new Application([
        'is_benefit' => true,
        'benefit_type' => $benefitType,
    ]);

    expect($application->hasPriorityBenefit())->toBeFalse();
})->with([
    'orphans' => Application::BenefitOrphans,
    'without parental care' => Application::BenefitWithoutParentalCare,
    'disabled children' => Application::BenefitDisabledChildren,
    'disabled group one' => Application::BenefitDisabledGroupOne,
    'disabled group two' => Application::BenefitDisabledGroupTwo,
    'disabled from childhood' => Application::BenefitDisabledFromChildhood,
]);

test('svo children benefit uses the default benefit badge label', function (string $benefitType) {
    $application = new Application([
        'is_benefit' => true,
        'benefit_type' => $benefitType,
    ]);

    expect($application->benefit_badge_label)->toBe('Льгота');
})->with([
    'svo children' => Application::BenefitSvoChildren,
    'legacy svo' => 'svo',
]);
