<?php

namespace Juzaweb\Modules\Movie\Enums;

enum ReportStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';

    public static function all()
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => trans('movie::translation.pending'),
            self::PROCESSED => trans('movie::translation.processed'),
        };
    }

    public function badge()
    {
        return match ($this) {
            self::PENDING => '<span class="badge badge-warning">'.$this->label().'</span>',
            self::PROCESSED => '<span class="badge badge-success">'.$this->label().'</span>',
            default => '<span class="badge badge-secondary">'.$this->value.'</span>',
        };
    }
}
