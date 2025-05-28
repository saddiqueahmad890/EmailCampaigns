<?php

namespace App\Http\Controllers;

use App\Models\ExclusionList;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExclusionListController extends Controller
{

    private $_request = null;
    private $_modal = null;


    public function __construct(Request $request, ExclusionList $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //  $data = $this->get_all($this->_modal);
       $data = ExclusionList::all();

        return view('exclusion-list.exclusivelist', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request to ensure an email is provided
        $request->validate([
            'email' => 'required|email|unique:exclusion_lists,email',
        ]);

        // Create a new exclusion entry
        $this->_modal->create([
            'email' => $request->email,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Email successfully added to exclusion list.');
    }


    /**
     * Display the specified resource.
     */
    public function show(ExclusionList $exclusionList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExclusionList $exclusionList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:exclusion_lists,email,' . $id,
        ]);
    
        $exclusion = ExclusionList::findOrFail($id);
        $exclusion->email = $request->email;
        $exclusion->save();
    
        return redirect()->back()->with('success', 'Email updated successfully.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $exclusion = ExclusionList::findOrFail($id);
        $exclusion->delete();
    
        return redirect()->back()->with('success', 'Email deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $exclusion = ExclusionList::findOrFail($id);
        $exclusion->status = $request->status;
        $exclusion->save();
    
        return redirect()->back()->with('success', 'Status updated successfully.');
    }
    
    public function csvUploading(Request $request)
    {
        try {
            // Validate the file
            $request->validate([
                'exclusion_csv' => 'required|mimes:csv,txt|max:2048',
            ]);

            // Read the file
            $file = $request->file('exclusion_csv');
            $handle = fopen($file->getPathname(), "r");

            if (!$handle) {
                return redirect()->back()->with('error', 'Error opening the file.');
            }

            // Skip the header row (if exists)
            fgetcsv($handle);

            // Process each row
            $emails = [];
            $invalidEmails = [];
            $duplicateEmails = [];

            // Get existing emails from the database
            $existingEmails = ExclusionList::pluck('email')->toArray();

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!empty($row[0])) {
                    $email = trim($row[0]); // Trim whitespace

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $invalidEmails[] = $email; // Collect invalid emails
                        continue;
                    }

                    // Check for duplicates
                    if (!in_array($email, $existingEmails)) {
                        $emails[] = [
                            'email' => $email,
                        ];
                        $existingEmails[] = $email; // Add to existing emails to prevent inserting again
                    } else {
                        $duplicateEmails[] = $email;
                    }
                }
            }

            fclose($handle);

            // Insert emails into the database (avoid duplicates)
            if (!empty($emails)) {
                ExclusionList::insert($emails);
            }

            // Prepare error messages
            $errorMessages = [];
            if (!empty($invalidEmails)) {
                $errorMessages[] = count($invalidEmails) . ' emails were invalid and were not added.';
            }
            if (!empty($duplicateEmails)) {
                $errorMessages[] = count($duplicateEmails) . ' emails were duplicates and were not added.';
            }

            // Return response
            if (!empty($errorMessages)) {
                return redirect()->back()->with('error', implode(' | ', $errorMessages));
            }

            return redirect()->back()->with('success', 'CSV file uploaded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
