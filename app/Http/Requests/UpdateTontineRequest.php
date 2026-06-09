<?php

namespace App\Http\Requests;

class UpdateTontineRequest extends StoreTontineRequest
{
    public function rules(): array
    {
        $tontine = $this->route('tontine');
        $nbActuels = $tontine->membres()->count();

        $rules = parent::rules();
        $rules['nombre_membres_max'] = [
            'required', 'integer', 'min:' . max(2, $nbActuels), 'max:100',
            function ($attribute, $value, $fail) use ($tontine, $nbActuels) {
                if ($value < $nbActuels) {
                    $fail('Le maximum ne peut pas être inférieur au nombre de membres actuels (' . $nbActuels . ').');
                }
            }
        ];

        return $rules;
    }
}
