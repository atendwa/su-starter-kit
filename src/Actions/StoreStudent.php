<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Actions;

use Atendwa\SuStarterKit\Models\User;
use Illuminate\Support\Collection;
use Throwable;

class StoreStudent
{
    /**
     * @throws Throwable
     */
    public function execute(Collection $payload): User
    {
        $shortname = $payload['faculties'] ?? '';

        $department = get_department_by_shortname($shortname);

        $attributes = ['username' => $payload['studentNo']];

        $otherNames = $payload['otherNames'];

        $firstName = explode(' ', $otherNames)[0];

        $otherNames = str_replace($firstName, '', $otherNames);

        $attributes['phone_number'] = $payload['mobileNo'];
        $attributes['department_id'] = $department?->id;
        $attributes['last_name'] = $payload['surname'];
        $attributes['other_names'] = trim($otherNames);
        $attributes['email'] = $payload['email'];
        $attributes['first_name'] = $firstName;

        return User::firstOrCreate($attributes, $attributes);
    }
}

//            'faculty' => $payload['faculties'],
//            'courses' => $payload['courses'],
//            'fatherMobileNo' => $payload['fatherMobileNo'],
//            'motherMobileNo' => $payload['motherMobileNo'],
//            'feeBalance' => $payload['feeBalance'],
//            'gender' => $payload['gender'],
//            'currentYearMedicalFee' => $payload['currentYearMedicalFee'],
//            'courses' => $payload['courses'],
//            'applicantId' => $payload['applicantId'],
