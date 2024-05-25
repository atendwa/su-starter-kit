<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Observers;

use Atendwa\SuStarterKit\Models\Department;
use Atendwa\SuStarterKit\Support\AttributeSanitizers\DepartmentSanitizer;

class DepartmentObserver
{
    public function creating(Department $department): void
    {
        $departmentSanitizer = new DepartmentSanitizer();

        $department->name = $departmentSanitizer->name($department->name);

        $department->code = $departmentSanitizer->shortName($department->code);
    }

    public function updating(Department $department): void
    {
        $departmentSanitizer = new DepartmentSanitizer();

        $department->name = $departmentSanitizer->name($department->name);

        $department->code = $departmentSanitizer->shortName($department->code);
    }
}
