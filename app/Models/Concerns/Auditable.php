<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Tempel trait ini di model yang aksinya mau otomatis kecatat di audit_logs.
 * Contoh: `class Aset extends Model { use Auditable; ... }`
 *
 * Field sensitif (password, dsb) otomatis disaring lewat $auditExclude,
 * override properti itu di model kalau perlu.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAuditLog('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $old = collect($changes)->keys()
                ->mapWithKeys(fn ($key) => [$key => $model->getOriginal($key)])
                ->all();

            $model->recordAuditLog('updated', $old, $changes);
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('deleted', $model->getOriginal(), null);
        });
    }

    protected function recordAuditLog(string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'description' => $this->auditDescription($action),
            'old_values' => $this->filterAuditValues($old),
            'new_values' => $this->filterAuditValues($new),
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 255),
        ]);
    }

    protected function filterAuditValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $excluded = property_exists($this, 'auditExclude')
            ? $this->auditExclude
            : ['password', 'remember_token'];

        return collect($values)->except($excluded)->all();
    }

    /** Override method ini di model kalau mau deskripsi yang lebih spesifik. */
    protected function auditDescription(string $action): string
    {
        $label = match ($action) {
            'created' => 'Menambahkan',
            'updated' => 'Mengubah',
            'deleted' => 'Menghapus',
            default => ucfirst($action),
        };

        return "{$label} " . class_basename($this) . " #{$this->getKey()}";
    }
}