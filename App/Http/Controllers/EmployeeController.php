<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class EmployeeController extends Controller
{
    /**
     * Lists the name of all Employees sorted alphabetically
     * @OA\Get (
     *     path="http://megainsight.dsv/api/employees",
     *     tags={"Employees"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="no_employee",
     *                         type="string",
     *                         example="Roberto Carlos"
     *                     ),
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $employees = Employee::orderby('no_employee')->pluck('no_employee')->toArray();
        return response()->json($employees,200); 
    }


    
    public function store(Request $request)
    {
        $this->validate($request,[
            'no_employee'    	=> "required|min:3|max:100|unique:employees,no_employee",
            'branch_id'    	    => "required",
            'role_id'    	    => "required",
        ]);

        
        DB::beginTransaction();
        try {
            $employee  = new Employee();
            $employee->no_employee  = $request->no_employee;
            $employee->branch_id    = $request->branch_id;
            $employee->role_id      = $request->role_id;
            //dd($employee);
    
            $employee->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th);
            return response()->json($th, 500); 
        }
        DB::commit();
        return response()->json($employee,201); 
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    
        $employee = Employee::with('branch','role')->find($id);

        if(!$employee) {
            return response()->json([
                'message'   => 'Employee not found',
            ], 404);
        }
        return response()->json($employee,200); 
    }

    public function employeename($no_employee)
    {
        $employee = Employee::where('no_employee',$no_employee)->first();
        if(!$employee) {
            return response()->json([
                'message'   => 'Employee not found',
            ], 404);
        }
        return response()->json($employee,200); 
    }

}
