<?php

namespace App\Observers\Tenants;

use App\Models\DynamicFormField;
use Illuminate\Http\Request;
use Auth;

class DynamicFormFieldObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Handle the DynamicFormField "creating" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function creating(DynamicFormField $dynamicFormField)
    {
        $dynamicFormField->user_id = Auth::user()->id;

        if (!$dynamicFormField->sort_no) {
            $maxSortNo = DynamicFormField::where('dynamic_form_page_id', $this->request->dynamic_form_page_id)
                ->where('user_id', Auth::user()->id)->max('sort_no') + 1;

            $dynamicFormField->sort_no = $maxSortNo;
        }
    }

    /**
     * Handle the DynamicFormField "created" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function created(DynamicFormField $dynamicFormField)
    {
        //
    }

    /**
     * Handle the DynamicFormField "updated" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function updated(DynamicFormField $dynamicFormField)
    {
        //
    }

    /**
     * Handle the DynamicFormField "deleted" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function deleted(DynamicFormField $dynamicFormField)
    {
        //
    }

    /**
     * Handle the DynamicFormField "restored" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function restored(DynamicFormField $dynamicFormField)
    {
        //
    }

    /**
     * Handle the DynamicFormField "force deleted" event.
     *
     * @param  \App\Models\DynamicFormField  $dynamicFormField
     * @return void
     */
    public function forceDeleted(DynamicFormField $dynamicFormField)
    {
        //
    }
}
