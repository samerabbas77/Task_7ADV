<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Api\ReoprtServices;


class ReportController extends Controller
{
    use ApiResponseTrait;

    protected $reportService;
    public function __construct(ReoprtServices $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Create Complete Task report.
     */

    public function create_Complete_Task_Report()
    {
        $report = $this->reportService->create_Complete_Task_Report();
        if($report)
        {
            return $this->success($report,"Show Completed Reports Successfully");
        }else{
            return $this->error(null,"There no Completed Task");
        }
         
    }

    /**
     * Create UnComplete Task report.
     */
    public function create_UnCompleted_Task_Report()
    {
        $report = $this->reportService->create_UnCompleted_Task_Report();

        if($report)
        {
            return $this->success($report,"Show UnCompleted Reports Successfully");
        }else{
            return $this->error(null,"There are not any NonCompleted Task");
        }
       

    }

    /**
     * Filter the reorts
     */

     public function getReportsByfilters(Request $request)
    {
        $report =  $this->reportService->getReportsByfilters($request->input('type'),$request->input('user'));
        if($report)
        {
            return $this->success($report,"Show  Reports Successfully");
        }
        else{
            return $this->error(null,"There no Result for ypur search");
        }
      

    }
}
