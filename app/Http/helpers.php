<?php

if (! function_exists('getFromID')) {
    function getFromID($id, $name){
    	$table = "$name";
		$temp = DB::table($table)
				->select('name')
				->where([
						['id',$id],
						['deleted_at',null]
						])
	            ->first();
		return $temp->name;
	}
}