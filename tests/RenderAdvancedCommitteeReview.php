<?php

// Fixtures visuales aisladas: nunca escriben en MySQL ni permiten enviar formularios.
require __DIR__.'/RenderFrontendReview.php';
use App\Models\AcademicCareer;
use App\Models\AcademicCommittee;
use App\Models\Defense;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use App\Services\CommitteeAssignment;
use App\Services\CommitteeLifecycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$manager = Role::where('name', 'COORDINACION')->firstOrFail()->users()->firstOrFail();
$practice = Practice::firstOrFail();
$practice->student->update(['academic_career_id' => AcademicCareer::where('name', 'Derecho')->value('id')]);
$fields = ['revision' => 0, 'status' => 'Activa', 'reason' => 'Asignación interdisciplinaria registrada', 'members' => []];
foreach (['Derecho', 'Psicología', 'Generales'] as $index => $faculty) {
    $user = User::factory()->create(['name' => ['Docente de Derecho', 'Docente de Psicología', 'Docente de Generales'][$index], 'status' => 'ACTIVE', 'role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->value('id')]);
    $teacher = Teacher::create(['user_id' => $user->id, 'access_approved_at' => now()]);
    $area = AcademicCareer::where('name', $faculty)->firstOrFail();
    $teacher->faculties()->attach($area->id, ['is_primary' => true]);
    $fields['members'][] = ['user_id' => $user->id, 'function' => AcademicCommittee::FUNCTIONS[$index], 'academic_career_id' => $area->id];
}
$committee = app(CommitteeAssignment::class)->save($practice, $manager, $fields);
$render = function ($name, $path) use ($kernel, $manager, $directory) {
    Auth::forgetGuards();
    Auth::setUser($manager);
    $response = $kernel->handle(Request::create('http://127.0.0.1:8000'.$path));
    if ($response->getStatusCode() !== 200) {
        throw new RuntimeException($path.' '.$response->getStatusCode());
    }
    file_put_contents($directory.'/'.$name.'.html', str_replace('</body>', '<script>document.addEventListener("submit",e=>e.preventDefault());</script></body>', $response->getContent()));
};
$render('ternas-derecho', '/coordinacion/ternas?faculty='.$practice->student->academic_career_id);
$render('terna-activa', '/coordinacion/ternas/'.$committee->id);
$render('terna-carga', '/coordinacion/ternas/carga-docente');
$teacher = Teacher::create(['user_id' => User::factory()->create(['name' => 'Docente sustituto de Psicología', 'status' => 'ACTIVE', 'role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->value('id')])->id, 'access_approved_at' => now()]);
$teacher->faculties()->attach($fields['members'][1]['academic_career_id'], ['is_primary' => true]);
$fields['members'][1]['user_id'] = $teacher->user_id;
$fields['revision'] = $committee->revision;
$fields['reason'] = 'Sustitución por disponibilidad académica';
$committee = app(CommitteeAssignment::class)->save($practice, $manager, $fields);
$render('terna-sustitucion', '/coordinacion/ternas/'.$committee->id);
$defense = Defense::create(['academic_committee_id' => $committee->id, 'duration_minutes' => 60, 'status' => 'Realizada', 'performed_at' => now()]);
$workflow = app(CommitteeLifecycle::class);
$workflow->act($committee, $manager, ['action' => 'result', 'result' => 'Aprobada', 'revision' => $committee->revision, 'round' => 1, 'reason' => 'Resultado de defensa registrado']);
$workflow->act($committee, $manager, ['action' => 'close', 'revision' => $committee->revision, 'round' => 1, 'final_records_verified' => true, 'reason' => 'Evaluación y documentación final verificadas']);
$render('terna-cerrada','/coordinacion/ternas/'.$committee->id);
$render('terna-carga-cerrada','/coordinacion/ternas/carga-docente?status=closed');
echo "Seis vistas adicionales de ternas renderizadas en memoria.\n";
