<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class ReportExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $controller = app(\App\Http\Controllers\ReportController::class);
        $data = $controller->getReportData($this->request);

        return view('reports.excel', $data);
    }
}
