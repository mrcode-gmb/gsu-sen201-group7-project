<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BusinessScoped
{
    /**
     * Get the current user's business ID
     */
    protected function getBusinessId()
    {
        $user = auth()->user();
        
        // Super Admin has no business restriction
        if ($user && $user->isSuperAdmin()) {
            return null;
        }
        
        return $user ? $user->business_id : null;
    }

    /**
     * Check if current user is Super Admin
     */
    protected function isSuperAdmin()
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Apply business scope to a query
     * If user is Super Admin, no scope is applied
     * Otherwise, filter by user's business_id
     */
    protected function applyBusinessScope(Builder $query)
    {
        if (!$this->isSuperAdmin()) {
            $businessId = $this->getBusinessId();
            if ($businessId) {
                $query->where('business_id', $businessId);
            }
        }
        
        return $query;
    }

    /**
     * Scope a model query to the current business
     */
    protected function scopeToCurrentBusiness($model)
    {
        $query = $model::query();
        return $this->applyBusinessScope($query);
    }

    /**
     * Ensure a model belongs to the current user's business
     * Throws 403 if not
     */
    protected function ensureBusinessOwnership($model)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $businessId = $this->getBusinessId();
        
        if ($model->business_id !== $businessId) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return true;
    }

    /**
     * Add business_id to data array if not Super Admin
     */
    protected function addBusinessId(array $data)
    {
        if (!$this->isSuperAdmin()) {
            $data['business_id'] = $this->getBusinessId();
        }

        return $data;
    }
}
