<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Employee;

/**
* @OA\Info(
*             title="Evaluation application for Megainsight", 
*             version="1.0",
* )
*
* @OA\Server(url="150.230.81.216/api")
*/


class BranchController extends Controller
{
    /**
     * Lists the name of all Branches sorted alphabetically
     * @OA\Get (
     *     path="/api/branchs",
     *     tags={"Branchs"},
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
     *                         property="no_branch",
     *                         type="string",
     *                         example="ACME co"
     *                     ),
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $branches = Branch::orderby('no_branch')->pluck('no_branch')->toArray();
        return response()->json($branches,200); 
    }

    
    
    /**
     * Create a new Branch
     * @OA\Post(
     *  path="/api/branch",
     *  tags={"Branchs"},
     *     @OA\Parameter(in="path",name="no_branch",required=true,@OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="no_branch",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "no_branch":"ACME LTDA.",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="no_branch", type="string", example="ACME LTDA"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The no branch has already been taken."),
     *          )
     *      ),
     * )
     */

    public function store(Request $request)
    {
        $this->validate($request,[
            'no_branch'    	=> "required|min:3|max:100|unique:branches,no_branch",
        ]);

        
        DB::beginTransaction();
        try {
            $branch  = new Branch();
            $branch->no_branch = $request->no_branch;
            //dd($branch);
    
            $branch->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th);
            return response()->json($th, 500); 
        }
        DB::commit();
        return response()->json($branch,201); 
    }


    /**
     * Shows branch information, searching by Id.
     * @OA\Get (
     *     path="/api/branchs/{branch_id}",
     *     tags={"Branchs"},
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
     *              @OA\Property(property="no_branch", type="string", example="ACME co"),
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

    public function show($id)
    {
    
        $branch = Branch::find($id);

        if(!$branch) {
            return response()->json([
                'message'   => 'Branch not found',
            ], 404);
        }
        return response()->json($branch,200); 
    }


    /**
     * Shows branch information, searching by Name.
     * @OA\Get (
     *     path="/api/branchname/{branch_name}",
     *     tags={"Branchs"},
     *     @OA\Parameter(
     *         in="path",
     *         name="no_branch",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="no_branch", type="string", example="ACME co"),
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
    public function branchname($no_branch)
    {
        $branch = Branch::where('no_branch',$no_branch)->first();
        if(!$branch) {
            return response()->json([
                'message'   => 'Branch not found',
            ], 404);
        }
        return response()->json($branch,200); 
    }


    /**
    * Shows the information of a Branch and all Employees related to it..
    * @OA\Get (
    *     path="/api/branchemployees/{branch_id}",
    *     tags={"Branchs"},
    *     @OA\Parameter(
    *         in="path",
    *         name="no_branch",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *               @OA\Property(property="id", type="number", example=1),
    *               @OA\Property(property="no_branch", type="string", example="ACME co"),
    *               @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
    *               @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z"),
    *               @OA\Property(
    *                  property="employees",
    *                  type="array",
    *                  @OA\Items(
    *                       type="object",
    *                       format="query",
    *                       @OA\Property(property="id", type="number", example=1),
    *                       @OA\Property(property="no_employee", type="string", example="Ayrton Senna" ),
    *                       @OA\Property(property="branch_id", type="number", example=1),
    *                       @OA\Property(property="role_id", type="number", example=1),
    *                       @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
    *                       @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z"),
    *                 ),
    *              ),
    *         ),
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
    public function branchemployees($branch)
    {
        $branch     = Branch::with('employees')->find($branch);

        if(!$branch) {
            return response()->json([
                'message'   => 'Branch not found',
            ], 404);
        }

        $employees  = Employee::with('role')->where('branch_id', $branch->id)->orderby('no_employee')->get();
        return response()->json([$branch, $employees],200); 
    }

}
