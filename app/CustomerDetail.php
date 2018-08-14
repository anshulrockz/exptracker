<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetail extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $connection = 'mysql2';

    public function category()
    {
        return $this->belongsTo('App\ClaimCategory', 'name', 'category');
    }

    public static function all_insrance_claims()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1)
		{
			$data = DB::connection('mysql2')
			->table('customer_details')
				->select('claim_job_details.*', 'claim_details.*', 'vehicle_details.*', 'customer_details.*')
				->where([
				// ['users.company_id',$company],
				['customer_details.deleted_at',null],
				// ['customer_details.id',50]
				])
	            ->leftJoin('claim_job_details', 'claim_job_details.customer_detail_id', '=', 'customer_details.id')
	            ->leftJoin('claim_details', 'claim_details.customer_detail_id', '=', 'customer_details.id')
	            ->leftJoin('vehicle_details', 'vehicle_details.customer_detail_id', '=', 'customer_details.id')
	            ->get();

	        foreach ($data as $key => $value) {
	        	$documents = DB::connection('mysql2')
	        	->table('documents')
				->select('documents.doc_type', 'documents.doc_file', 'documents.doc_status')
				->where([
				// ['users.id',$user_id],
				['documents.deleted_at',null],
				['documents.customer_detail_id',$value->id]
				])
				->orderBy('id', 'desc')
				->get();

				$job_entries = DB::connection('mysql2')
	        	->table('claim_job_entries')
				->select('claim_job_entries.entry_payment_date','claim_job_entries.entry_payment_amt')
				->where([
				// ['users.id',$user_id],
				['claim_job_entries.deleted_at',null],
				['claim_job_entries.customer_detail_id',$value->id]
				])
				->get();

	        	$value->documents = $documents;
	        	$value->job_entries = $job_entries;
	        }

	        return $data;
		}
		
		else
		{
			return DB::table('customer_details')
				->select('customer_details.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.id',$user_id],
				['customer_details.deleted_at',null],
				['customer_details.mode',2]
				])
	            ->leftJoin('users', 'users.id', '=', 'customer_details.created_by')
	            ->leftJoin('workshops', 'workshops.id', '=', 'customer_details.location_id')
	            ->get();
		}
	}
}
