// --- 1. Définition des messages Flash ---
export interface FlashMessages {
    success?: string;
    error?: string;
    info?: string;
    warning?: string;
}

// --- 2. Définition des propriétés de l'utilisateur (Auth) ---
interface UserProps {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    plan_id: number | null;
    current_team_id: number | null;
    stripe_id: string | null;
    created_at?: string;
    updated_at?: string;
}

interface AuthProps {
    user: UserProps | null;
}

// --- 3. Définition des propriétés Ziggy (pour les routes Laravel en JS) ---
interface ZiggyRoute {
    uri: string;
    methods: string[];
    domain?: string;
    bindings?: Record<string, string>;
    wheres?: Record<string, string>;
}

interface ZiggyProps {
    url: string;
    port: number | null;
    routes: Record<string, ZiggyRoute>;
    location: string;
}

// --- 4. Définition de TES PageProps spécifiques, qui incluent toutes les données partagées ---
// C'est cette interface qui sera utilisée comme base pour surcharger les PageProps globales d'Inertia.
export interface CustomSharedPageProps { // Renommé pour plus de clarté
    // Props par défaut d'Inertia qui sont souvent présentes
    errors: Record<string, string>; // Les erreurs de validation de Laravel

    // Tes props spécifiques du middleware HandleInertiaRequests.php
    name: string;
    quote: {
        message: string;
        author: string;
    };
    auth: AuthProps;
    ziggy: ZiggyProps;
    sidebarOpen: boolean;
    flash: FlashMessages;
}

// --- 5. Redéfinir l'interface PageProps globale de Inertia ---
/* eslint-disable @typescript-eslint/no-empty-object-type */
declare global {
    namespace Inertia {
        interface PageProps extends CustomSharedPageProps {}
    }
}
/* eslint-enable @typescript-eslint/no-empty-object-type */

export {}; // Nécessaire pour traiter le fichier comme un module
