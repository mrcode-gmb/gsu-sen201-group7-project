<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $business = auth()->user()->business;
        $documents = $business->documents()->with('user')->latest()->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,png,doc,docx,xls,xlsx|max:10240', // 10MB Max
            'name' => 'required|string|max:255',
        ]);

        $business = auth()->user()->business;
        $file = $request->file('document');

        // Store the file in a business-specific directory
        $path = $file->store('documents/' . $business->id, 'public');

        $business->documents()->create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'path' => $path,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Document $document)
    {
        // Ensure the user is authorized to delete the document
        if ($document->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the file from storage
        Storage::disk('public')->delete($document->path);

        // Delete the record from the database
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }
}
