<?php

namespace App\Http\Requests;

// ✅ Bonne pratique : l'update hérite du store
// On n'a pas besoin de redéfinir les règles identiques
class UpdateMembreRequest extends StoreMembreRequest
{
    // Toutes les règles de StoreMembreRequest s'appliquent
    // Le $id dans rules() gère déjà l'unicité pour l'update
}
