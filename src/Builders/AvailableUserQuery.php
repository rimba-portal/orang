<?php

declare(strict_types=1);

namespace Rimba\People\Builders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rimba\People\Models\Staff;

class AvailableUserQuery
{
    /**
     * Users available for assignment to a Staff record.
     *
     * A user is available when:
     * - the user is not assigned to any Staff; or
     * - the user is already assigned to the Staff currently being edited.
     *
     * @return Builder<User>
     */
    public static function make(?Staff $staff = null): Builder
    {
        return User::query()
            ->where(function (Builder $query) use ($staff): void {
                $query->whereDoesntHave('staff');

                if ($staff?->user_id) {
                    $query->orWhereKey($staff->user_id);
                }
            });
    }
}
