# palm-oil-shop

composer require livewire/livewire

php artisan wallets:create-for-businesses

@show.blade.php#L1-276 good from this add button for fund wallet and withdrawal for super admin and make ever wallet blade files are inside super admin folder please



UPDATE wallets
SET balance =
(
    (
        (SELECT COALESCE(SUM(balance), 0)
         FROM business_capitals
         WHERE business_id = 4)
      + (SELECT COALESCE(SUM(total_amount), 0)
         FROM sales
         WHERE business_id = 4)
    )
    -
    (
        (SELECT COALESCE(SUM(total_cost), 0)
         FROM purchase_histories
         WHERE business_id = 4)
      + (SELECT COALESCE(SUM(amount), 0)
         FROM expenses
         WHERE business_id = 4)
    )
)
WHERE business_id = 4
LIMIT 1;
