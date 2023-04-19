<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class EmployeeController extends Controller
{
    /**
     * Lists the name of all Employees sorted alphabetically
     * @OA\Get (
     *     path="/api/employees",
     *     security={{ "bearerAuth": {} }},
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

    
    /**
     * Create a new Employee
     * @OA\Post(
     *  path="/api/employee",
     *  tags={"Employees"},
     *     @OA\Parameter(in="path",name="no_employee",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(in="path",name="branch_id",required=true,@OA\Schema(type="number")),
     *     @OA\Parameter(in="path",name="role_id",required=true,@OA\Schema(type="number")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="no_employee",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="branch_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="role_id",
     *                          type="number"
     *                      )
     *                 ),
     *                 example={
     *                     "no_employee":"José da Silva",
     *                     "branch_id":"1",
     *                     "role_id":"1",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="no_employee", type="string", example="José da Silva"),
     *              @OA\Property(property="branch_id", type="number", example="1"),
     *              @OA\Property(property="role_id", type="number", example="1"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The no employee has already been taken."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=428,
     *          description="Precondition Required",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Role not found."),
     *          )
     *      )
     * )
     */
    
    public function store(Request $request)
    {
        $this->validate($request,[
            'no_employee'    	=> "required|min:3|max:100|unique:employees,no_employee",
            'branch_id'    	    => "required",
            'role_id'    	    => "required",
        ]);

        $role   = Role::find($request->role_id);
        $branch = Branch::find($request->branch_id);
        
        if(is_null($role))
            return response()->json("Role not found", 428); 
        
        if(is_null($branch))
            return response()->json("Branch not found", 428); 

        
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
     * Show employee information, searching by Id.
     * @OA\Get (
     *     path="/api/employee/{employee_id}",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="no_employee", type="string", example="João Lopes"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Employee not found"),
     *          )
     *      )
     * )
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

    /**
     * Show employee information, searching by name.
     * @OA\Get (
     *     path="/api/employeename/{employee_name}",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         in="path",
     *         name="no_employee",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="no_employee", type="string", example="José Santos"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Branch not found"),
     *          )
     *      )
     * )
     */
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
