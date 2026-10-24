<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmergencyContactAdminController extends Controller
{
    // Usa los métodos CRUD (index, create, store, edit, update, destroy)
    
    public function index()
    {
        $contacts = EmergencyContact::all();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function create()
    {
        $contact = new EmergencyContact(); 
        return view('admin.contacts.form', compact('contact'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_link' => 'nullable|url|max:255',
        ]);

        EmergencyContact::create($validated);
        return redirect()->route('admin.contacts.index')->with('success', 'Contacto de emergencia creado con éxito.');
    }

    public function edit(EmergencyContact $contact)
    {
        return view('admin.contacts.form', compact('contact'));
    }

    public function update(Request $request, EmergencyContact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_link' => 'nullable|url|max:255',
        ]);

        $contact->update($validated);
        return redirect()->route('admin.contacts.index')->with('success', 'Contacto de emergencia actualizado con éxito.');
    }

    public function destroy(EmergencyContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Contacto eliminado correctamente.');
    }
}