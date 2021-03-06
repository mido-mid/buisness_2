<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $companies = DB::select(DB::raw('
                                select * from packaging_companies where packaging_companies.stateId = 1 and
                                packaging_companies.country_id = "' . auth()->user()->country_id . '"'));

        foreach ($companies as $company) {
            $phones = DB::select(DB::raw('
                                    select packaging_companies_phones.phoneNumber from packaging_companies_phones
                                    where packaging_companies_phones.packaging_company_id =
                                    ' . $company->id));

            $company->phones = $phones;
        }

        return view('User.companies.index',compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function search(Request $request,$search_param)
    {
        if($search_param != "null") {

            $lang = App::getlocale();

            $companies = DB::select(DB::raw('
                                select * from packaging_companies where packaging_companies.stateId = 1 and
                                packaging_companies.name_' . $lang . ' LIKE "%' . $search_param . '%"'));

            foreach ($companies as $company) {
                $phones = DB::select(DB::raw('
                                    select packaging_companies_phones.phoneNumber from packaging_companies_phones
                                    where packaging_companies_phones.packaging_company_id =
                                    ' . $company->id));

                $company->phones = $phones;
            }
        }
        else {
            $companies = DB::select(DB::raw('
                                    select * from packaging_companies where packaging_companies.stateId = 1 and
                                    packaging_companies.country_id = "' . auth()->user()->country_id . '"'));

            foreach ($companies as $company) {
                $phones = DB::select(DB::raw('
                                        select packaging_companies_phones.phoneNumber from packaging_companies_phones
                                        where packaging_companies_phones.packaging_company_id =
                                        ' . $company->id));

                $company->phones = $phones;
            }
        }

        $view = view('includes.partialcompanies', compact('companies'));

        $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

        return $sections['companies'];
    }
}
