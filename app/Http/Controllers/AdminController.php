<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class AdminController extends Controller
{
    /**
     * Show admin contacts page (dashboard sidebar)
     */
    public function index()
    {
        // Lấy danh sách contacts có phân trang để dùng cho common-table
        $rows = Contact::latest()->paginate(10);

        return view('admin.contacts.index', compact('rows'));
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully!',
                'contact' => $contact
            ]);
        }

        return redirect()->route('admin.contacts.index')->with('success', 'Contact created successfully!');
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully!',
                'contact' => $contact->fresh()
            ]);
        }

        return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully!');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Request $request, Contact $contact)
    {
        $contact->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully!'
            ]);
        }

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully!');
    }
}
