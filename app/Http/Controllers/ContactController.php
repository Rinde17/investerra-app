<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Affiche la page de contact.
     */
    public function index(): Response
    {
        return Inertia::render('Contact');
    }

    /**
     * Traite la soumission du formulaire de contact.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        // 2. Sauvegarde du message en base de données (BONNE PRATIQUE !)
        try {
            ContactMessage::create($validatedData);
        } catch (\Exception $e) {
            // Log l'erreur ou retourne une réponse d'erreur si la BDD échoue
            Log::error('Erreur lors de la sauvegarde du message de contact en BDD: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Impossible de sauvegarder votre message pour le moment. Veuillez réessayer.']);
        }


        // 3. Envoi de l'e-mail
        try {
            // Remplacez 'email_de_reception@exemple.com' par l'adresse où on veut recevoir les messages
            Mail::to('email_de_reception@exemple.com')->send(new ContactFormMail($validatedData));
        } catch (\Exception $e) {
            // Log l'erreur si l'envoi d'email échoue
            Log::error('Erreur lors de l\'envoi de l\'email de contact: ' . $e->getMessage());
            return redirect()->back()->withErrors(['email_error' => 'Votre message a été enregistré, mais l\'envoi de l\'email a échoué.']);
        }


        // 4. Redirection avec un message de succès
        return redirect()->back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}
