<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Test;

use Validator;
use Illuminate\Http\JsonResponse;

class TestController extends BaseController
{
    public function get_test(Request $request, $athleteId) {
        $tests = Test::with('user')->where('user_id', $athleteId)->get();

        return $this->sendResponse($tests, '');
    }

    public function show(Request $request, $id) {
        $test = Test::with('user')->find($id);

        return $this->sendResponse($test, '');
    }

    public function insert_result( Request $request ) {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'test_date' => 'required|date',
            'test_results' => 'nullable|array',
            'test_results.*' => 'nullable|numeric|min:0',
        ];
    
        // Validate the request
        $validator = Validator::make($request->all(), $rules);
    
        // Check for validation failures
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
    
        // Retrieve and prepare the test results
        $testResults = $request->input('test_results', []);
        $testResultsJson = json_encode($testResults);
    
        // Prepare data for insertion
        $data = $request->only(['user_id', 'test_date']);
        $data['test_results'] = $testResultsJson;
    
        // Create a new test record
        $test = Test::create($data);
    
        // Decode the test results for response
        $test->test_results = json_decode($test->test_results);
    
        // Return a success response
        return $this->sendResponse($test, 'Test result stored successfully.');
    }

    public function update_result(Request $request, $id) {
        // Define validation rules
        $rules = [
            'test_date' => 'required|date',
            'test_results' => 'nullable|array',
            'test_results.*' => 'nullable|numeric|min:0', // Ensure test results are positive numbers
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Check for validation failures
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Retrieve the test record by ID
        $test = Test::find($id);

        // Check if the record exists
        if (!$test) {
            return $this->sendError('Test record not found.');
        }

        // Prepare the test results
        $testResults = $request->input('test_results', []);
        $testResultsJson = json_encode($testResults);

        // Update the test record
        $test->test_date = $request->input('test_date');
        $test->test_results = $testResultsJson;
        $test->save();

        // Decode the test results for response
        $test->test_results = json_decode($test->test_results);

        // Return a success response
        return $this->sendResponse($test, 'Test result updated successfully.');
    }
}
