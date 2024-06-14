<?php

namespace Modules\Spreadsheet\Providers;
use  Modules\Spreadsheet\Entities\Related;
use  Modules\Spreadsheet\Entities\Spreadsheets;

use Illuminate\Support\ServiceProvider;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public function boot()
    {

        view()->composer(['taskly::projects.show'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route == "projects.show")
            {
                try {
                    $ids = \Request::segment(2);
                    if(!empty($ids))
                    {
                        try {
                            $id = $ids;
                            $related = Related::where('model_name','Project')->first();
                            // dd('$related');
                            $spreadsheets = Spreadsheets::where('related',$related->id)->get();
                            $module = 'Project';

                            if(!is_null($spreadsheets) && (is_array($spreadsheets) || count($spreadsheets) > 0))
                            {
                                $view->getFactory()->startPush('addButtonHook', view('spreadsheet::layouts.addhook', compact('id','related','module')));
                            }

                        } catch (\Throwable $th)
                        {
                             
                        }
                    }
                } catch (\Throwable $th) {

                }
            }
        });

        view()->composer(['contract::contracts.show'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route == "contract.show")
            {
                try {
                    $ids = \Request::segment(2);

                    if(!empty($ids))
                    {
                        try {
                            $id = $ids;
                            $related = Related::where('model_name','Contract')->first();
                            $spreadsheets = Spreadsheets::where('related',$related->id)->get();

                            if(!is_null($spreadsheets) && (is_array($spreadsheets) || count($spreadsheets) > 0))
                            {
                                $view->getFactory()->startPush('contractButtonHook', view('spreadsheet::layouts.addhook', compact('id','related')));
                            }

                        } catch (\Throwable $th)
                        {

                        }
                    }
                } catch (\Throwable $th) {

                }
            }
        });

        view()->composer(['lead::leads.show'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route == "leads.show")
            {
                try {
                    $ids = \Request::segment(2);
                    if(!empty($ids))
                    {
                        try {
                            $id = $ids;
                            $related = Related::where('model_name','Lead')->first();
                            $spreadsheets = Spreadsheets::where('related',$related->id)->get();

                            if(!is_null($spreadsheets) && (is_array($spreadsheets) || count($spreadsheets) > 0))
                            {
                                $view->getFactory()->startPush('addButtonHook0', view('spreadsheet::layouts.addhook', compact('id','related')));
                            }

                        } catch (\Throwable $th)
                        {

                        }
                    }
                } catch (\Throwable $th) {

                }
            }
        });

        view()->composer(['lead::deals.show'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route == "deals.show")
            {
                try {
                    $ids = \Request::segment(2);

                    if(!empty($ids))
                    {
                        try {
                            $id = $ids;
                            $related = Related::where('model_name','Deal')->first();
                            $spreadsheets = Spreadsheets::where('related',$related->id)->get();

                            if(!is_null($spreadsheets) && (is_array($spreadsheets) || count($spreadsheets) > 0))
                            {
                                $view->getFactory()->startPush('addButtonHook', view('spreadsheet::layouts.addhook', compact('id','related')));
                            }

                        } catch (\Throwable $th)
                        {

                        }
                    }
                } catch (\Throwable $th) {

                }
            }
        });
    }

    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
