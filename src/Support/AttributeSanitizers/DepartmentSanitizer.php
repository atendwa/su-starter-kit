<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Support\AttributeSanitizers;

use Illuminate\Support\Str;

class DepartmentSanitizer
{
    public function name(string $name): string
    {
        $name = self::removeStringsFromName($name);

        $name = self::formatNameSymbols($name);

        $isAllCaps = is_a_match($name, mb_strtoupper($name));

        return match (both($isAllCaps, str_contains($name, ' '))) {
            true => Str::title(Str::lower($name)),
            false => $name
        };
    }

    public function shortName(string $shortName): string
    {
        $shortName = str_replace('  ', ' ', $shortName);
        $shortName = str_replace('-', ' - ', $shortName);
        $shortName = str_replace('  ', ' ', $shortName);
        $shortName = Str::lower($shortName);

        return Str::trim($shortName);
    }

    private function removeStringsFromName(string $name): string
    {
        $stringsToRemove = [
            '(SU)', '(CSC)', '(ADMISSIONS)', '(SBS)', '(FINANCE)', '(FHM)',
            '(SIMS)', '(SLS)', '(ADMIN)', '(PMD)', '(DOS)', '(SI)', '-PFD',
            '(SMC)', '(MKTR MNGR SBS)', '-SBS', '- SBS', ',SBS', '- EE',
        ];

        $name = str_replace($stringsToRemove, '', $name);

        return str_replace('STH-', 'Strathmore ', $name);
    }

    private function formatNameSymbols(string $name): string
    {
        $name = str_replace('( ', '(', $name);
        $name = str_replace('-', ' - ', $name);
        $name = str_replace(',', ' , ', $name);
        $name = str_replace('   ', '  ', $name);
        $name = str_replace('  ', ' ', $name);
        $name = str_replace(' ,', ', ', $name);

        return str_replace('  ', ' ', $name);
    }
}
