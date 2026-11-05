<?php
namespace App\Service;

/** Common filtering for the small public venue/facility/opportunity catalogues. */
final class PublicCatalogue
{
    public function filter(array $records, string $keyword = '', string $sort = 'created_desc', string $category = ''): array
    {
        $keyword = mb_strtolower(trim($keyword));
        $records = array_values(array_filter($records, static function ($record) use ($keyword, $category): bool {
            if (method_exists($record, 'getHidden') && $record->getHidden()) return false;
            if (method_exists($record, 'isHidden') && $record->isHidden()) return false;
            if (method_exists($record, 'getPublished') && !$record->getPublished()) return false;
            if (method_exists($record, 'isPublished') && !$record->isPublished()) return false;
            if (method_exists($record, 'getDeletedAt') && $record->getDeletedAt()) return false;
            $name = method_exists($record, 'getTitle') ? $record->getTitle() : $record->getName();
            $description = method_exists($record, 'getDescription') ? $record->getDescription() : $record->getShortdescription();
            if ($keyword !== '' && $keyword !== 'all' && !str_contains(mb_strtolower($name.' '.strip_tags($description ?? '')), $keyword)) return false;
            if ($category !== '' && $category !== 'all') {
                $value = method_exists($record, 'getCategory') ? $record->getCategory() : null;
                if (!$value || $value->getSlug() !== $category) return false;
            }
            return true;
        }));
        usort($records, static function ($a, $b) use ($sort): int {
            $name = static fn($item) => method_exists($item, 'getTitle') ? $item->getTitle() : $item->getName();
            $comparison = match ($sort) {
                'title_asc' => strnatcasecmp($name($a), $name($b)),
                'title_desc' => strnatcasecmp($name($b), $name($a)),
                'created_asc' => $a->getCreatedAt() <=> $b->getCreatedAt(),
                default => $b->getCreatedAt() <=> $a->getCreatedAt(),
            };
            return $comparison ?: ($a->getId() <=> $b->getId());
        });
        return $records;
    }
}
