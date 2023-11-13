<?php

namespace App\Observers\Tenants;

use App\Models\DynamicFormPage;
use Auth;

class DynamicFormPageObserver
{
    /**
     * Handle the DynamicFormPage "creating" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function creating(DynamicFormPage $dynamicFormPage)
    {
        $dynamicFormPage->user_id = Auth::user()->id;

        if (!$dynamicFormPage->sort_no) {
            $maxSortNo = DynamicFormPage::where('user_id', Auth::user()->id)->max('sort_no') + 1;

            $dynamicFormPage->sort_no = $maxSortNo;
        }
    }

    /**
     * Handle the DynamicFormPage "created" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function created(DynamicFormPage $dynamicFormPage)
    {
        //
    }

    /**
     * Handle the DynamicFormPage "updated" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function updated(DynamicFormPage $dynamicFormPage)
    {
        //
    }

    /**
     * Handle the DynamicFormPage "deleted" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function deleted(DynamicFormPage $dynamicFormPage)
    {
        //
    }

    /**
     * Handle the DynamicFormPage "restored" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function restored(DynamicFormPage $dynamicFormPage)
    {
        //
    }

    /**
     * Handle the DynamicFormPage "force deleted" event.
     *
     * @param  \App\Models\DynamicFormPage  $dynamicFormPage
     * @return void
     */
    public function forceDeleted(DynamicFormPage $dynamicFormPage)
    {
        //
    }
}
