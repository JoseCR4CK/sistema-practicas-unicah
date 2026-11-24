<?php

namespace Tests\Feature;

use App\Models\AccessRequest;
use App\Models\AcademicCareer;
use App\Models\AcademicDocument;
use App\Models\AcademicSubject;
use App\Models\DocumentVersion;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AcademicClassTime;
use App\Services\ImportAcademicCatalog;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccessAdmissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('access_documents');
    }

    private function faculty(): AcademicCareer { return AcademicCareer::studentOptions()->firstOrFail(); }
    private function ca(): User { return User::whereHas('role', fn ($q) => $q->where('name', 'COORDINACION'))->firstOrFail(); }

    private function payload(bool $student = true, array $extra = []): array
    {
        $fields = ['first_names' => 'Persona', 'last_names' => 'Escenario', 'dni' => 'REG-00001',
            'email' => 'registro@example.test', 'phone' => '00000000', 'academic_career_id' => $this->faculty()->id,
            'password' => 'Scenario123!', 'password_confirmation' => 'Scenario123!'];
        if ($student) {
            $fields += ['account_number' => 'CUENTA-ESCENARIO', 'has_pending_subjects' => 1,
                'subjects' => $this->faculty()->subjects()->limit(3)->pluck('academic_subjects.id')->all(), 'documents' => []];
            foreach (AcademicDocument::ACCESS_TYPES as $key => $label) $fields['documents'][$key] = [UploadedFile::fake()->create($key.'.pdf', 20, 'application/pdf')];
        }
        return array_replace($fields, $extra);
    }

    private function submit(bool $student = true, array $extra = []): AccessRequest
    {
        $this->post($student ? '/registro/estudiante' : '/registro/docente', $this->payload($student, $extra))->assertSessionHasNoErrors()->assertRedirect();
        return AccessRequest::latest('id')->firstOrFail();
    }

    private function act(AccessRequest $application, string $action, array $extra = [])
    {
        return $this->actingAs($this->ca())->post(route('coordinacion.acceso.review', $application),
            array_replace(['revision' => $application->fresh()->revision, 'action' => $action], $extra));
    }

    private function reviewDocuments(AccessRequest $application): void
    {
        foreach ($application->documents as $document) {
            $this->act($application, 'document', ['document_id' => $document->id, 'version_id' => $document->currentVersion->id, 'result' => DocumentVersion::APPROVED])->assertSessionHasNoErrors();
        }
    }

    public function test_catalog_is_normalized_and_import_is_idempotent_without_inventing_missing_codes(): void
    {
        $this->assertSame(8, AcademicCareer::studentOptions()->count());
        $this->assertDatabaseHas('academic_careers', ['name' => 'Generales', 'type' => 'GENERAL', 'status' => 'ACTIVE']);
        $this->assertFalse(AcademicCareer::where('status', 'ACTIVE')->where('name', 'like', '%Medicina%')->exists());
        $this->assertSame(11, AcademicSubject::where('subject_type', 'GENERAL')->count());
        $this->assertSame(7, AcademicSubject::where('subject_type', 'COPROGRAMMATIC')->count());
        $this->assertSame(14, AcademicSubject::where('subject_type', 'ELECTIVE')->whereNull('code')->count());
        $this->assertSame(4, AcademicSubject::where('subject_type', 'ENGLISH')->where('credits', 7)->where('weekly_minutes', 400)->count());
        $shared = AcademicSubject::where('code', 'ES101')->firstOrFail();
        $this->assertSame(1, AcademicSubject::where('code', 'ES101')->count());
        $this->assertSame(9, $shared->faculties()->count());
        $conflict = AcademicSubject::where('code', 'PS385')->firstOrFail();
        $this->assertTrue($conflict->review_required);
        $this->assertNull($conflict->credits);
        $this->assertNull($conflict->confirmedWeeklyHours());
        $count = AcademicSubject::count();
        $shared->update(['status' => 'INACTIVE']);
        app(ImportAcademicCatalog::class)->run();
        $this->assertSame($count, AcademicSubject::count());
        $this->assertSame('INACTIVE', $shared->fresh()->status);
    }

    public function test_credit_rules_support_one_laboratories_and_more_than_five_credits(): void
    {
        foreach ([1 => 1, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 12 => 13] as $credits => $hours) {
            $this->assertSame($hours, AcademicClassTime::hoursForCredits($credits));
        }
        $this->assertSame(2, AcademicClassTime::hoursForCredits(2, 'LABORATORY'));
        $this->assertNull(AcademicClassTime::hoursForCredits(null));
        $this->assertNull(AcademicClassTime::hoursForCredits(2.5));
        $this->assertSame(400, AcademicClassTime::hoursForCredits(7) * AcademicClassTime::CLASS_MINUTES);
    }

    public function test_public_forms_and_student_submission_store_documents_and_three_subjects_without_creating_user(): void
    {
        $this->get('/registro/estudiante')->assertOk()->assertDontSee('>Generales<', false)->assertSee('documents[DNI_ESTUDIANTE][]', false);
        $this->get('/registro/docente')->assertOk()->assertSee('Generales')->assertDontSee('name="documents[', false);
        $users = User::count();
        $application = $this->submit(true, ['role_id' => $this->ca()->role_id, 'status' => 'APPROVED', 'induction_verified' => 1]);
        $this->assertSame($users, User::count());
        $this->assertSame('PENDING', $application->status);
        $this->assertFalse($application->induction_verified);
        $this->assertTrue(Hash::check('Scenario123!', $application->password_hash));
        $this->assertArrayNotHasKey('password_hash', $application->toArray());
        $this->assertSame(3, $application->subjects()->count());
        $this->assertSame(7, $application->documents()->count());
        foreach ($application->documents as $document) Storage::disk('access_documents')->assertExists($document->currentVersion->files[0]['path']);
        $this->actingAs($this->ca())->get(route('coordinacion.acceso', $application))->assertOk()->assertSee('El estudiante declaró más de 2 asignaturas pendientes');
    }

    public function test_validation_rejects_invalid_files_wrong_faculty_duplicates_and_password_mismatch(): void
    {
        $this->post('/registro/estudiante', [])->assertSessionHasErrors(['dni', 'email', 'password', 'documents']);
        $this->post('/registro/estudiante', $this->payload(true, ['password_confirmation' => 'otra']))->assertSessionHasErrors('password');
        $general = AcademicCareer::where('type', 'GENERAL')->firstOrFail();
        $this->post('/registro/estudiante', $this->payload(true, ['academic_career_id' => $general->id]))->assertSessionHasErrors('academic_career_id');
        $outside = AcademicSubject::whereDoesntHave('faculties', fn ($q) => $q->where('academic_careers.id', $this->faculty()->id))->firstOrFail();
        $this->post('/registro/estudiante', $this->payload(true, ['subjects' => [$outside->id]]))->assertSessionHasErrors('subjects');
        $fields = $this->payload();
        Storage::disk('access_documents')->put('invalid.txt', 'texto que no es un PDF');
        $fields['documents']['SOLVENCIA_PAGO'] = [new UploadedFile(Storage::disk('access_documents')->path('invalid.txt'), 'archivo.pdf', null, null, true)];
        $this->post('/registro/estudiante', $fields)->assertSessionHasErrors('documents.SOLVENCIA_PAGO.0');
        $fields = $this->payload();
        $fields['documents']['SOLVENCIA_PAGO'] = [UploadedFile::fake()->create('grande.pdf', 8193, 'application/pdf')];
        $this->post('/registro/estudiante', $fields)->assertSessionHasErrors('documents.SOLVENCIA_PAGO.0');
        $application = $this->submit();
        $this->post('/registro/estudiante', $this->payload())->assertSessionHasErrors(['dni', 'email', 'account_number']);
        $this->assertSame(1, AccessRequest::count());
    }

    public function test_ca_approval_requires_induction_documents_and_creates_all_associations_atomically(): void
    {
        $application = $this->submit();
        $beforeUsers = User::count();
        $beforeProfiles = Student::count();
        $this->act($application, 'start')->assertSessionHasNoErrors();
        $this->act($application, 'approve')->assertSessionHasErrors('approval');
        $this->assertSame($beforeUsers, User::count());
        $this->act($application, 'induction', ['induction_verified' => 1])->assertSessionHasNoErrors();
        $this->act($application, 'approve')->assertSessionHasErrors('approval');
        $this->reviewDocuments($application);
        $this->act($application, 'approve')->assertSessionHasNoErrors();
        $application->refresh();
        $this->assertSame('APPROVED', $application->status);
        $this->assertSame($beforeUsers + 1, User::count());
        $this->assertSame($beforeProfiles + 1, Student::count());
        $this->assertTrue($application->user->hasRole('ESTUDIANTE'));
        $this->assertTrue(Hash::check('Scenario123!', $application->user->password));
        $student = $application->user->student;
        $this->assertSame(3, $student->pendingSubjects()->count());
        $this->assertSame(7, $student->documents()->count());
        $this->assertSame(0, $student->practices()->count());
        $this->assertSame($application->academic_career_id, $student->academic_career_id);
        $this->act($application, 'approve')->assertConflict();
        $this->actingAs($application->user)->get('/estudiante')->assertOk()->assertSee('Asignaturas cursadas durante la práctica');
        auth()->forgetGuards();
        $this->post('/login', ['dni' => $application->dni, 'password' => 'Scenario123!'])->assertRedirect(route('estudiante.dashboard'));
    }

    public function test_document_correction_keeps_versions_and_private_tracking_is_required(): void
    {
        $response = $this->post('/registro/estudiante', $this->payload())->assertSessionHasNoErrors();
        $url = $response->headers->get('Location');
        $application = AccessRequest::firstOrFail();
        $this->get($url)->assertOk()->assertDontSee('access-documents/')->assertDontSee('password_hash');
        $this->get(route('registro.estado', [$application, str_repeat('x', 64)]))->assertNotFound();
        $this->act($application, 'start');
        $document = $application->documents()->where('type', 'SOLVENCIA_PAGO')->firstOrFail();
        $old = $document->currentVersion;
        $this->act($application, 'document', ['document_id' => $document->id, 'version_id' => $old->id, 'result' => DocumentVersion::CORRECTION])->assertSessionHasErrors('note');
        $this->act($application, 'document', ['document_id' => $document->id, 'version_id' => $old->id, 'result' => DocumentVersion::CORRECTION, 'note' => 'Documento ilegible'])->assertSessionHasNoErrors();
        $this->act($application, 'correct', ['note' => 'Reenviar únicamente la solvencia legible'])->assertSessionHasNoErrors();
        $this->get($url)->assertOk()->assertSee('Documento ilegible');
        auth()->forgetGuards();
        $fields = $this->payload(true, ['documents' => ['SOLVENCIA_PAGO' => [UploadedFile::fake()->create('nueva.pdf', 20, 'application/pdf')]], 'revision' => $application->fresh()->revision]);
        $this->put($url, $fields)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('PENDING', $application->fresh()->status);
        $this->assertSame(2, $document->versions()->count());
        Storage::disk('access_documents')->assertExists($old->files[0]['path']);
        $this->assertTrue($application->events()->where('action', 'Corrección enviada')->exists());
    }

    public function test_teacher_needs_no_documents_or_induction_and_automatically_appears_in_faculty_catalog(): void
    {
        $general = AcademicCareer::where('type', 'GENERAL')->firstOrFail();
        $application = $this->submit(false, ['academic_career_id' => $general->id, 'role' => 'ROOT']);
        $this->assertSame(0, $application->documents()->count());
        $this->assertNull($application->user_id);
        $this->act($application, 'start')->assertSessionHasNoErrors();
        $this->act($application, 'approve')->assertSessionHasNoErrors();
        $application->refresh();
        $teacher = $application->user->teacher;
        $this->assertTrue($application->user->hasRole('DOCENTE_SEGUIMIENTO'));
        $this->assertFalse($application->induction_verified);
        $this->assertTrue($teacher->faculties()->whereKey($general->id)->exists());
        $this->assertTrue(Teacher::approvedActive()->whereKey($teacher->id)->exists());
        $this->actingAs($this->ca())->get(route('coordinacion.docentes', ['faculty' => $general->id]))->assertOk()->assertViewHas('docentes', fn ($teachers) => $teachers->contains('id', $teacher->id));
        $this->put(route('coordinacion.docente.facultad', $teacher), ['academic_career_id' => $this->faculty()->id])->assertSessionHasNoErrors();
        $this->assertTrue($teacher->faculties()->whereKey($this->faculty()->id)->wherePivot('is_primary', true)->exists());
        $this->assertFalse($teacher->faculties()->whereKey($general->id)->exists());
        $this->get(route('coordinacion.docentes', ['faculty' => $this->faculty()->id]))->assertOk()->assertViewHas('docentes', fn ($teachers) => $teachers->contains('id', $teacher->id));
        $application->user->update(['status' => 'INACTIVE']);
        $this->assertFalse(Teacher::approvedActive()->whereKey($teacher->id)->exists());
    }

    public function test_approval_conflicts_roll_back_and_only_ca_can_review_or_download(): void
    {
        $application = $this->submit(false);
        $this->act($application, 'start');
        $existing = User::firstOrFail();
        $application->update(['dni' => $existing->dni]);
        $before = User::count();
        $this->act($application, 'approve')->assertSessionHasErrors('approval');
        $this->assertSame($before, User::count());
        $this->assertNull($application->fresh()->user_id);
        $this->assertSame('IN_REVIEW', $application->fresh()->status);
        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO'] as $role) {
            $user = User::whereHas('role', fn ($q) => $q->where('name', $role))->firstOrFail();
            $this->actingAs($user)->post(route('coordinacion.acceso.review', $application), ['revision' => $application->revision, 'action' => 'approve'])->assertForbidden();
            $this->get(route('coordinacion.acceso', $application))->assertForbidden();
        }
        $this->act($application, 'reject', ['note' => 'Datos en conflicto'])->assertSessionHasNoErrors();
        $this->assertSame('REJECTED', $application->fresh()->status);
        $this->assertDatabaseHas('access_request_events', ['access_request_id' => $application->id, 'action' => 'Solicitud rechazada']);
    }

    public function test_files_require_private_token_or_ca_or_approved_student_ownership(): void
    {
        $response = $this->post('/registro/estudiante', $this->payload())->assertSessionHasNoErrors();
        $token = basename($response->headers->get('Location'));
        $application = AccessRequest::firstOrFail();
        $version = $application->documents()->firstOrFail()->currentVersion;
        $this->get(route('registro.archivo', [$application, $token, $version, 0]))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get(route('registro.archivo', [$application, str_repeat('x', 64), $version, 0]))->assertNotFound();
        $this->get(route('academico.archivo-acceso', [$version, 0]))->assertRedirect(route('login'));
        $otherStudent = User::whereHas('role', fn ($q) => $q->where('name', 'ESTUDIANTE'))->firstOrFail();
        $this->actingAs($otherStudent)->get(route('academico.archivo-acceso', [$version, 0]))->assertForbidden();
        $this->act($application, 'start');
        $this->reviewDocuments($application);
        $this->act($application, 'induction', ['induction_verified' => 1]);
        $this->act($application, 'approve')->assertSessionHasNoErrors();
        $this->actingAs($application->fresh()->user)->get(route('academico.archivo-acceso', [$version, 0]))->assertOk();
        $this->get(route('academico.archivo-acceso', [$version, 99]))->assertNotFound();
    }

    public function test_enfermeria_has_no_automatic_special_block_and_no_subjects_can_be_declared(): void
    {
        $faculty = AcademicCareer::where('name', 'Enfermería')->firstOrFail();
        $application = $this->submit(true, ['academic_career_id' => $faculty->id, 'has_pending_subjects' => 0, 'subjects' => []]);
        $this->assertFalse($application->has_pending_subjects);
        $this->assertSame(0, $application->subjects()->count());
        $this->act($application, 'start');
        $this->reviewDocuments($application);
        $this->act($application, 'induction', ['induction_verified' => 1]);
        $this->act($application, 'approve')->assertSessionHasNoErrors();
        $this->assertSame('APPROVED', $application->fresh()->status);
    }

    public function test_profile_failure_rolls_back_user_creation_and_approval(): void
    {
        $application = $this->submit();
        $this->act($application, 'start');
        $this->reviewDocuments($application);
        $this->act($application, 'induction', ['induction_verified' => 1]);
        $count = User::count();
        Student::creating(function (Student $student) {
            if ($student->account_number === 'CUENTA-ESCENARIO') throw new \RuntimeException('Fallo simulado del almacenamiento de perfil');
        });
        try {
            $this->act($application, 'approve')->assertStatus(500);
            $this->assertSame($count, User::count());
            $this->assertSame('IN_REVIEW', $application->fresh()->status);
            $this->assertNull($application->user_id);
            $this->assertFalse(User::where('dni', $application->dni)->exists());
        } finally {
            Student::flushEventListeners();
        }
    }

    public function test_public_csrf_and_source_fingerprint_are_verified(): void
    {
        $source = json_decode(file_get_contents(database_path('data/academic_catalog.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($source['sha256'], hash_file('sha256', base_path($source['source'])));
        $this->assertCount(483, array_slice($source['sheets']['PLANES_CARRERA'], 1));
        $this->app['env'] = 'local';
        try {
            $this->post('/registro/docente', $this->payload(false))->assertStatus(419);
        } finally {
            $this->app['env'] = 'testing';
        }
    }

    public function test_teacher_approved_directly_in_a_faculty_is_visible_without_manual_catalog_entry(): void
    {
        $application = $this->submit(false);
        $this->act($application, 'start');
        $this->act($application, 'approve')->assertSessionHasNoErrors();
        $teacher = $application->fresh()->user->teacher;
        $this->assertTrue($teacher->faculties()->whereKey($this->faculty()->id)->wherePivot('is_primary', true)->exists());
        $this->get(route('coordinacion.docentes', ['faculty' => $this->faculty()->id]))->assertOk()->assertViewHas('docentes', fn ($teachers) => $teachers->contains('id', $teacher->id));
    }
}
