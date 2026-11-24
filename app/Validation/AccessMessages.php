<?php

namespace App\Validation;

use App\Models\AcademicDocument;
use Illuminate\Http\Request;

// Mensajes locales al Bloque 6A: no cambia el idioma ni las reglas de otros módulos.
class AccessMessages
{
    public static function messages(Request $request): array
    {
        $note = match ($request->input('action')) {
            'reject' => 'Debe indicar el motivo para rechazar la solicitud.',
            'correct' => 'Debe indicar qué debe corregir el solicitante.',
            default => 'Debe indicar la observación para solicitar la corrección del documento.',
        };

        return [
            'required' => 'Debe completar :attribute.',
            'required_if' => 'Debe completar :attribute para continuar.',
            'string' => 'Debe escribir texto en :attribute.',
            'integer' => 'Debe indicar un número entero para :attribute.',
            'boolean' => 'Seleccione una opción válida para :attribute.',
            'array' => 'La selección de :attribute no es válida.',
            'max' => 'El contenido de :attribute no debe superar los :max caracteres.',
            'min' => 'El valor mínimo permitido para :attribute es :min.',
            'unique' => 'El valor de :attribute ya está registrado en el sistema.',
            'email' => 'Debe indicar un correo electrónico válido.',
            'exists' => 'La opción seleccionada para :attribute no es válida.',
            'in' => 'Seleccione una opción válida para :attribute.',
            'distinct' => 'No debe repetir elementos en :attribute.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
            'file' => 'Debe adjuntar un archivo válido para :attribute.',
            'uploaded' => 'No se pudo recibir :attribute. Compruebe el tamaño y vuelva a intentarlo.',
            'mimes' => 'El archivo de :attribute debe ser PDF, JPG, JPEG o PNG.',
            'mimetypes' => 'El contenido de :attribute debe corresponder a un PDF, JPG, JPEG o PNG válido.',
            'extensions' => 'La extensión de :attribute debe ser PDF, JPG, JPEG o PNG.',
            'dni.unique' => 'El DNI ya está registrado en el sistema.',
            'email.unique' => 'El correo electrónico ya está registrado en el sistema.',
            'account_number.unique' => 'El DNI indicado ya está registrado como número de cuenta de un estudiante.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe superar los 128 caracteres.',
            'academic_career_id.exists' => 'Seleccione una facultad o área válida para este registro.',
            'subjects.required_if' => 'Seleccione las asignaturas pendientes que desea declarar.',
            'note.required_if' => $note,
            'note.required' => $note,
            'note.max' => 'La observación no debe superar los 5000 caracteres.',
            'result.required_if' => 'Seleccione un resultado para la revisión documental.',
            'result.in' => 'Seleccione un resultado válido para la revisión documental.',
            'revision.required' => 'Faltan datos de la revisión. Recargue la solicitud antes de continuar.',
            'documents.*.*.max' => 'Cada archivo debe tener un tamaño máximo de 8 MB.',
            'documents.*.max' => 'Adjunte como máximo dos archivos por documento.',
            'documents.*.min' => 'Debe adjuntar al menos un archivo por documento.',
        ];
    }

    public static function attributes(): array
    {
        $attributes = [
            'first_names' => 'los nombres', 'last_names' => 'los apellidos', 'dni' => 'el DNI',
            'account_number' => 'el DNI', 'email' => 'el correo electrónico', 'phone' => 'el teléfono',
            'academic_career_id' => 'la facultad o área', 'password' => 'la contraseña',
            'password_confirmation' => 'la confirmación de contraseña', 'has_pending_subjects' => 'la declaración de asignaturas pendientes',
            'subjects' => 'las asignaturas pendientes', 'subjects.*' => 'las asignaturas pendientes',
            'documents' => 'los documentos obligatorios', 'note' => 'la observación', 'revision' => 'la revisión',
            'action' => 'la acción de revisión', 'induction_verified' => 'la verificación de asistencia a inducción',
            'document_id' => 'el documento', 'version_id' => 'la versión del documento', 'result' => 'el resultado',
            'faculty' => 'la facultad o área', 'type' => 'el tipo de solicitud', 'status' => 'el estado', 'search' => 'la búsqueda',
        ];
        foreach (AcademicDocument::ACCESS_TYPES as $type => $label) {
            $attributes['documents.'.$type] = $label;
            $attributes['documents.'.$type.'.*'] = $label;
        }
        return $attributes;
    }
}
