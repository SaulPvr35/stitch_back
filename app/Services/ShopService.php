<?php

namespace App\Services;

use App\Models\GameProgress;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserShopItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShopService
{
    public function balance(User $user): int
    {
        $earned = GameProgress::where('user_id', $user->id)->sum('coins_earned');
        $spent = UserShopItem::query()
            ->join('shop_items', 'shop_items.id', '=', 'user_shop_items.shop_item_id')
            ->where('user_shop_items.user_id', $user->id)
            ->sum('shop_items.price');

        return max(0, $user->profile->base_coins + $earned - $spent);
    }

    public function catalogFor(User $user): array
    {
        $owned = UserShopItem::where('user_id', $user->id)->get()->keyBy('shop_item_id');
        $isAdmin = $user->is_admin;
        $equippedItems = ShopItem::whereIn('id', $owned->filter(fn ($inventory) => $inventory->equipped)->keys())->get();
        $customization = ['theme' => null, 'skin' => null, 'pet' => null, 'decoration' => null, 'title' => null, 'hints' => 0];
        foreach ($equippedItems as $equippedItem) {
            $effect = $equippedItem->metadata['effect'] ?? null;
            $value = $equippedItem->metadata['value'] ?? null;
            if ($effect === 'hint') $customization['hints']++;
            elseif (array_key_exists($effect, $customization)) $customization[$effect] = $value;
        }

        return [
            'items' => ShopItem::where('active', true)->orderBy('category')->orderBy('price')->get()
                ->map(fn (ShopItem $item) => [
                    ...$item->toArray(),
                    'owned' => $isAdmin || $owned->has($item->id),
                    'equipped' => (bool) ($owned->get($item->id)?->equipped),
                    'admin_unlocked' => $isAdmin,
                ])->values(),
            'balance' => $this->balance($user),
            'customization' => $customization,
        ];
    }

    public function purchase(User $user, ShopItem $item): array
    {
        return DB::transaction(function () use ($user, $item): array {
            if ($user->is_admin) {
                UserShopItem::firstOrCreate([
                    'user_id' => $user->id,
                    'shop_item_id' => $item->id,
                ], [
                    'purchased_at' => now(),
                    'equipped' => false,
                ]);

                return $this->catalogFor($user);
            }

            if (UserShopItem::where('user_id', $user->id)->where('shop_item_id', $item->id)->exists()) {
                throw new RuntimeException('Este artículo ya está en tu colección.');
            }

            if ($this->balance($user) < $item->price) {
                throw new RuntimeException('No tienes suficientes monedas.');
            }

            UserShopItem::create([
                'user_id' => $user->id,
                'shop_item_id' => $item->id,
                'purchased_at' => now(),
                'equipped' => false,
            ]);

            return $this->catalogFor($user);
        });
    }

    public function toggleEquipped(User $user, ShopItem $item): array
    {
        if (!$user->is_admin && !UserShopItem::where('user_id', $user->id)->where('shop_item_id', $item->id)->exists()) {
            throw new RuntimeException('Debes conseguir este artículo antes de equiparlo.');
        }

        $inventory = UserShopItem::firstOrCreate(
            ['user_id' => $user->id, 'shop_item_id' => $item->id],
            ['purchased_at' => now(), 'equipped' => false],
        );
        $inventory->update(['equipped' => !$inventory->equipped]);

        return $this->catalogFor($user);
    }
}
