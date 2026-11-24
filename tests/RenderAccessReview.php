<?php

// Evidencia visual aislada del Bloque 6A. No consulta MySQL ni crea cuentas persistentes.
require __DIR__.'/RenderFrontendReview.php';

use App\Models\AccessRequest;
use App\Models\AcademicCareer;
use App\Models\AcademicSubject;
use App\Models\User;
use App\Services\RegisterAccessRequest;
use App\Services\ReviewAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

Storage::fake('access_documents');
$faculty = AcademicCareer::studentOptions()->firstOrFail();
$fields = ['first_names' => 'Solicitante', 'last_names' => 'Escenario visual', 'dni' => 'ESCENARIO-VISUAL',
    'email' => 'visual@example.test', 'phone' => '00000000', 'account_number' => 'CUENTA-VISUAL',
    'academic_career_id' => $faculty->id, 'password' => 'Scenario123!', 'password_confirmation' => 'Scenario123!',
    'has_pending_subjects' => 1, 'subjects' => $faculty->subjects()->limit(3)->pluck('academic_subjects.id')->all()];
$files = [];
foreach (App\Models\AcademicDocument::ACCESS_TYPES as $key => $label) $files[$key] = [UploadedFile::fake()->create($key.'.pdf', 20, 'application/pdf')];
$request = Request::create('/registro/estudiante', 'POST', $fields, files: ['documents' => $files]);
[$application, $token] = app(RegisterAccessRequest::class)->save($request, 'STUDENT');
$coordinator = User::whereHas('role', fn ($query) => $query->where('name', 'COORDINACION'))->firstOrFail();
app(ReviewAccessRequest::class)->execute($application, $coordinator, ['revision' => 0, 'action' => 'start']);
$cases = [
    ['registro-estudiante', null, '/registro/estudiante'], ['registro-docente', null, '/registro/docente'],
    ['accesos-ca', $coordinator, '/coordinacion/solicitudes-acceso'],
    ['acceso-ca-detalle', $coordinator, '/coordinacion/solicitudes-acceso/'.$application->id],
    ['acceso-estado', null, '/registro/solicitudes/'.$application->id.'/'.$token],
];
foreach ($cases as [$name, $user, $url]) {
    Auth::forgetGuards();
    if ($user) Auth::setUser($user);
    $response = $kernel->handle(Request::create('http://127.0.0.1:8000'.$url));
    if ($response->getStatusCode() !== 200) throw new RuntimeException($url.' respondió '.$response->getStatusCode());
    $html = str_replace('</body>', '<script>document.addEventListener("submit",event=>event.preventDefault());</script></body>', $response->getContent());
    file_put_contents($directory.'/'.$name.'.html', $html);
}
echo json_encode(['faculties' => AcademicCareer::studentOptions()->pluck('name'),
    'subjects' => AcademicSubject::count(), 'types' => AcademicSubject::selectRaw('subject_type, count(*) as total')->groupBy('subject_type')->pluck('total', 'subject_type'),
    'career_subject_relations' => Illuminate\Support\Facades\DB::table('academic_career_subject')->count(),
    'review_required' => AcademicSubject::where('review_required', true)->count(),
    'source' => 'XLSX exportado a JSON determinista; SQLite en memoria'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).PHP_EOL;
