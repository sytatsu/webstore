<?php

namespace App\Services;

use Illuminate\Support\Str;

class BundleSessionService
{
    private const KEY_BUNDLE_ID = 'active_bundle_id';
    private const KEY_BUNDLE_NAME = 'active_bundle_name';
    private const KEY_DISCOUNT_PCT = 'active_bundle_discount_pct';
    private const KEY_COLLECTION_ID = 'active_bundle_collection_id';

    public function getBundleId(): ?string
    {
        return session(self::KEY_BUNDLE_ID);
    }

    public function getBundleName(): string
    {
        return session(self::KEY_BUNDLE_NAME, '');
    }

    public function getCollectionId(): ?int
    {
        return session(self::KEY_COLLECTION_ID);
    }

    public function getDiscountPct(): float
    {
        return (float) session(self::KEY_DISCOUNT_PCT, 0);
    }

    public function startNewBundle(int $collectionId, string $name = ''): string
    {
        $bundleId = (string) Str::uuid();

        session([
            self::KEY_BUNDLE_ID => $bundleId,
            self::KEY_BUNDLE_NAME => $name,
            self::KEY_COLLECTION_ID => $collectionId,
            self::KEY_DISCOUNT_PCT => 0,
        ]);

        return $bundleId;
    }

    public function setName(string $name): void
    {
        session([self::KEY_BUNDLE_NAME => $name]);
    }

    public function setDiscount(float $pct): void
    {
        session([self::KEY_DISCOUNT_PCT => $pct]);
    }

    public function clearBundle(): void
    {
        session()->forget([
            self::KEY_BUNDLE_ID,
            self::KEY_BUNDLE_NAME,
            self::KEY_DISCOUNT_PCT,
            self::KEY_COLLECTION_ID,
        ]);
    }
}
