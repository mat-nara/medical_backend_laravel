<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Patient;
use PatientAccess;
use LogActivity;
use Auth;



class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient documents data isn\'t available'], 404);
        }

        $documents = Document::where('patient_id', $patient)->get();
        return ['data' =>  $documents, 'permission' => $permission];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Documents data'], 404);
        }

        if(!$request->hasFile('document')){
            return response(['error' => 1, 'message' => 'Invalid input. Please provide document file'], 400);
        }

        //Upload file and save data
        $path           = $request->file('document')->store('public/documents');
        $storage_path   = str_replace('public', 'storage', $path);
        $document       = Document::create([
            'patient_id'    => $patient,
            'filename'      => $request->file('document')->getClientOriginalName(),
            'MIME_type'     => $request->file('document')->getClientMimeType(),
            'path'          => $storage_path,
            'description'   => $request->description
        ]);

        $patient      = Patient::find($patient);
        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Document for patient: '. $patient->nom . ' ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Document stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patient, $document)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient documents data isn\'t available'], 404);
        }

        $document = Document::find($document);
        return ['data' =>  $document, 'permission' => $permission];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patient, $document)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Document data'], 404);
        }

        $document               = Document::find($document);
        $document->description  = $request->description ?? $document->description;
       
        if($request->hasFile('document')){
            $path                   = $request->file('document')->store('public/documents');
            $storage_path           = str_replace('public', 'storage', $path);
            $document->filename     = $request->file('document')->getClientOriginalName();
            $document->MIME_type    = $request->file('document')->getClientMimeType();
            $document->path         = $storage_path ?? $document->path;
        }

        $document->update();

        $patient        = Patient::find($patient);
        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Document data for the patient '. $patient->nom . ' ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Document updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($patient, $document)
    {
        //Check for permission access
        $permission     = PatientAccess::viewPermission($patient);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update patient\'s Document data'], 404);
        }

        $document = Document::find($document);
        $document->delete();

        $patient    = Patient::find($patient);
        $user       = Auth::user();
        LogActivity::addToLog($user->name . ' deleted the Document data for the patient '. $patient->nom . '  ' . $patient->prenoms);
        return response(['error' => 0, 'message' => 'Document deleted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($patient, $document)
    {
        //Check for permission access
        $permission = PatientAccess::viewPermission($patient);
        if($permission == 'unauthorized'){
            return response(['error' => 1, 'message' => 'Patient documents data isn\'t available'], 404);
        }

        $document = Document::find($document);
        return Response::download($document->path, $document->filename);
    }

}
