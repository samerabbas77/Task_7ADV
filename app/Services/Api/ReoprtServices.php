<?php
namespace App\Services\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Report;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReoprtServices
{
    use ApiResponseTrait;
   public function create_Complete_Task_Report()
{
    try {
        $tasks = Task::select('id','title','status')
                        ->where('status','Completed')
                        ->get();
        
        if($tasks->isEmpty())
        {
            return false;
        }
        $report = Report::create([
            'details'=> "Tasks has been completed Successfully:".$tasks->pluck('title'),
            "date" => Carbon::now(),
            "type"=> "Completed",
            "created_by"=> Auth::user()->full_name
        ])  ;    
        return $report;     
    } catch (Exception $e) {
        Log::error("Error while fetch the Completed Reports".$e->getMessage());
        throw new HttpResponseException($this->error(null, 'there is something wrong in server'.$e->getMessage(), 500));
    }
}

//................................................................
//................................................................

public function create_UnCompleted_Task_Report()
{
    try {
        $tasks = Task::select('id','title','status','due_date','assigned_to')
                    ->where('status','!=','Completed')
                    ->get();
        
        if($tasks->isEmpty())
        {
            return false;
        }
        $report = Report::create([
            'details'=> "Tasks not completed yet:".$tasks->pluck('title'),
            "date" => Carbon::now(),
            "type"=> "UnCompleted",
            "created_by"=> Auth::user()->full_name
        ]) ;
        return $report;
    } catch (Exception $e) {
        Log::error("Error while fetch the uncompleted Reports".$e->getMessage());
        throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
    }
} 
//......................................................................
//......................................................................
public function getReportsByfilters($type=null,$user=null)
{
    try {
        $report = Report::select('id','type','created_by')
                        ->when($type,function($q) use($type)
                            {
                                $q->where('type',$type);
                            })
                        ->when($user,function($q) use($user)
                            {
                                $q->where('created_by','LIKE','%'.$user.'%');
                            })
                            ->get();
        
        if($report->isEmpty())
        {
            return false;
        }

        return $report;
                
    } catch (Exception $e) {
        Log::error("Error while fetch the filtered Reports".$e->getMessage());
        throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
    }
}

}
