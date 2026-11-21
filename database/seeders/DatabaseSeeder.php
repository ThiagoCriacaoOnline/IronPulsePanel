<?php

namespace Database\Seeders;

use App\Models\ApiAccessLog;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\ProfessionalContact;
use App\Models\ProfessionalPayoutSettings;
use App\Models\ProfessionalProfile;
use App\Models\ProfessionalSpecialty;
use App\Models\ProfessionalStudent;
use App\Models\Rating;
use App\Models\ReportExport;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\StudentInvite;
use App\Models\SupportTicket;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Usuários do backoffice (senha padrão da UserFactory: "password")
        $admin = User::factory()->create([
            'name' => 'Thiago Dantas',
            'email' => 'admin@ironpulse.com.br',
        ]);

        $support = User::factory()->create([
            'name' => 'Camila Souza',
            'email' => 'suporte@ironpulse.com.br',
        ]);

        $finance = User::factory()->create([
            'name' => 'Bruno Lima',
            'email' => 'financeiro@ironpulse.com.br',
        ]);

        // 2. Configurações da plataforma
        $settings = [
            ['payments', 'platform_fee_percentage', '10', 'decimal', 'Taxa padrão da plataforma (%)', false],
            ['payments', 'payout_delay_days', '7', 'integer', 'Prazo em dias para liberação de repasse', false],
            ['media', 'max_video_seconds', '30', 'integer', 'Duração máxima de vídeos enviados', true],
            ['invites', 'default_invite_expiration_days', '7', 'integer', 'Validade padrão dos convites de aluno (dias)', false],
            ['legal', 'current_terms_version', '1.0', 'string', 'Versão vigente dos termos de uso', true],
            ['general', 'maintenance_mode', '0', 'boolean', 'Ativa o modo de manutenção da plataforma', false],
        ];

        foreach ($settings as [$group, $key, $value, $type, $description, $isPublic]) {
            SystemSetting::factory()->create([
                'group' => $group,
                'key' => $key,
                'value' => $value,
                'value_type' => $type,
                'description' => $description,
                'is_public' => $isPublic,
                'updated_by' => $admin->id,
            ]);
        }

        // 3. Permissões (módulo x ação + permissões específicas)
        $modules = [
            'users' => 'Usuários',
            'roles' => 'Perfis de acesso',
            'permissions' => 'Permissões',
            'professionals' => 'Profissionais',
            'students' => 'Alunos',
            'ratings' => 'Avaliações',
            'support' => 'Suporte',
            'reports' => 'Relatórios',
            'settings' => 'Configurações',
        ];

        $actions = [
            'view' => 'Visualizar',
            'create' => 'Criar',
            'update' => 'Editar',
            'delete' => 'Excluir',
        ];

        $permissions = collect();

        foreach ($modules as $module => $label) {
            foreach ($actions as $action => $actionLabel) {
                $permissions->push(Permission::factory()->create([
                    'name' => "{$actionLabel} {$label}",
                    'slug' => "{$module}.{$action}",
                    'description' => 'Permite '.Str::lower($actionLabel).' '.Str::lower($label).'.',
                    'module' => $module,
                    'is_sensitive' => $action === 'delete' || $module === 'settings',
                    'created_by' => $admin->id,
                ]));
            }
        }

        $extraPermissions = [
            ['professionals.verify', 'Verificar profissionais', 'professionals', true],
            ['professionals.payout', 'Gerenciar repasses', 'professionals', true],
            ['ratings.moderate', 'Moderar avaliações', 'ratings', false],
            ['support.assign', 'Atribuir chamados', 'support', false],
            ['reports.export', 'Exportar relatórios', 'reports', true],
        ];

        foreach ($extraPermissions as [$slug, $name, $module, $sensitive]) {
            $permissions->push(Permission::factory()->create([
                'name' => $name,
                'slug' => $slug,
                'description' => "Permite {$name}.",
                'module' => $module,
                'is_sensitive' => $sensitive,
                'created_by' => $admin->id,
            ]));
        }

        // 4. Perfis de acesso + vínculo com permissões
        $roleDefinitions = [
            'super-admin' => [
                'name' => 'Super Administrador',
                'description' => 'Acesso total ao backoffice.',
                'is_system' => true,
                'patterns' => ['*'],
            ],
            'admin' => [
                'name' => 'Administrador',
                'description' => 'Gestão de usuários, profissionais, alunos e relatórios.',
                'is_system' => true,
                'patterns' => ['users.*', 'roles.view', 'professionals.*', 'students.*', 'ratings.*', 'support.*', 'reports.*'],
            ],
            'support' => [
                'name' => 'Suporte',
                'description' => 'Atendimento de chamados e moderação de avaliações.',
                'is_system' => false,
                'patterns' => ['students.view', 'professionals.view', 'ratings.view', 'ratings.moderate', 'support.*'],
            ],
            'finance' => [
                'name' => 'Financeiro',
                'description' => 'Acompanhamento de repasses e relatórios.',
                'is_system' => false,
                'patterns' => ['professionals.view', 'professionals.payout', 'reports.view', 'reports.export'],
            ],
        ];

        $roles = collect($roleDefinitions)->map(fn (array $definition, string $slug) => Role::factory()->create([
            'name' => $definition['name'],
            'slug' => $slug,
            'description' => $definition['description'],
            'is_system' => $definition['is_system'],
            'created_by' => $admin->id,
        ]));

        foreach ($roleDefinitions as $slug => $definition) {
            $permissions
                ->filter(fn (Permission $permission) => collect($definition['patterns'])
                    ->contains(fn (string $pattern) => Str::is($pattern, $permission->slug)))
                ->each(fn (Permission $permission) => RolePermission::factory()->create([
                    'role_id' => $roles[$slug]->id,
                    'permission_id' => $permission->id,
                    'created_by' => $admin->id,
                ]));
        }

        foreach ([[$admin, 'super-admin'], [$support, 'support'], [$finance, 'finance']] as [$user, $roleSlug]) {
            UserRole::factory()->create([
                'user_id' => $user->id,
                'role_id' => $roles[$roleSlug]->id,
                'created_by' => $admin->id,
            ]);
        }

        // 5. Especialidades padrão
        $specialties = collect([
            ['name' => 'Hipertrofia', 'slug' => 'hipertrofia', 'profession_type' => 'personal'],
            ['name' => 'Emagrecimento', 'slug' => 'emagrecimento', 'profession_type' => 'both'],
            ['name' => 'Reabilitação e Mobilidade', 'slug' => 'reabilitacao', 'profession_type' => 'personal'],
            ['name' => 'Preparação Física / Lutas', 'slug' => 'preparacao-lutas', 'profession_type' => 'personal'],
            ['name' => 'Nutrição Esportiva', 'slug' => 'nutricao-esportiva', 'profession_type' => 'nutritionist'],
            ['name' => 'Recomposição Corporal', 'slug' => 'recomposicao-corporal', 'profession_type' => 'both'],
        ])->map(fn (array $item) => Specialty::factory()->create([
            ...$item,
            'created_by' => $admin->id,
        ]));

        // 6. Profissional 1: Personal Trainer aprovado
        $personal = ProfessionalProfile::factory()->personal()->approved()->create([
            'name' => 'Carlos Mendes',
            'email' => 'personal@ironpulse.com.br',
            'council_number' => '054321',
            'council_state' => 'RJ',
            'bio' => 'Educador físico com 8 anos de experiência em prescrição individualizada de treinos de força e hipertrofia.',
            'verified_by' => $admin->id,
            'created_by' => $admin->id,
        ]);

        $this->fillProfessional(
            $personal,
            $specialties,
            $admin,
            ['hipertrofia', 'emagrecimento', 'preparacao-lutas'],
            [
                'whatsapp' => '5521999991111',
                'public_email' => 'carlos@ironpulse.com.br',
                'instagram' => '@carlosmendes.personal',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'attendance_mode' => 'both',
            ],
        );

        // 7. Profissional 2: Nutricionista aprovada
        $nutri = ProfessionalProfile::factory()->approved()->create([
            'name' => 'Dra. Marina Costa',
            'email' => 'nutri@ironpulse.com.br',
            'council_number' => '20100987',
            'council_state' => 'RJ',
            'bio' => 'Atendimento presencial e online focado em performance, ganho de massa magra e emagrecimento sustentável.',
            'verified_by' => $admin->id,
            'created_by' => $admin->id,
        ]);

        $this->fillProfessional(
            $nutri,
            $specialties,
            $admin,
            ['nutricao-esportiva', 'recomposicao-corporal'],
            [
                'whatsapp' => '5521988882222',
                'public_email' => 'marina@ironpulse.com.br',
                'instagram' => '@marinacosta.nutri',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'attendance_mode' => 'both',
            ],
            ['payout_mode' => 'platform_intermediation', 'custom_fee_percentage' => 8.00],
        );

        // 8. Profissional 3: cadastro pendente (fila de verificação) e um rejeitado
        $pending = ProfessionalProfile::factory()->personal()->create([
            'name' => 'Rafael Rocha',
            'email' => 'pendente@ironpulse.com.br',
            'council_number' => '099887',
            'council_state' => 'SP',
            'bio' => 'Treinamento funcional e corrida.',
            'created_by' => $admin->id,
        ]);

        $this->fillProfessional($pending, $specialties, $admin);

        $rejected = ProfessionalProfile::factory()->rejected()->create([
            'name' => 'Patrícia Gomes',
            'email' => 'rejeitada@ironpulse.com.br',
            'verified_by' => $admin->id,
            'created_by' => $admin->id,
        ]);

        $this->fillProfessional($rejected, $specialties, $admin);

        // 9. Profissionais extras para testar paginação e filtros
        ProfessionalProfile::factory(8)
            ->approved()
            ->sequence(
                ['profession_type' => 'nutritionist', 'council_type' => 'crn'],
                ['profession_type' => 'personal', 'council_type' => 'cref'],
            )
            ->create(['verified_by' => $admin->id, 'created_by' => $admin->id])
            ->each(fn (ProfessionalProfile $professional) => $this->fillProfessional($professional, $specialties, $admin));

        // 10. Aluno modelo com vínculos e convites
        $lucas = Student::factory()->invitedBy($personal)->create([
            'name' => 'Lucas Almeida',
            'email' => 'aluno@ironpulse.com.br',
            'phone' => '21977773333',
            'birth_date' => '1996-05-15',
            'gender' => 'male',
            'goal' => 'hypertrophy',
            'created_by' => $admin->id,
        ]);

        $linkPersonal = ProfessionalStudent::factory()->create([
            'professional_profile_id' => $personal->id,
            'student_id' => $lucas->id,
            'service_type' => 'training',
            'started_at' => now()->subMonth(),
            'created_by' => $admin->id,
        ]);

        $linkNutri = ProfessionalStudent::factory()->nutrition()->create([
            'professional_profile_id' => $nutri->id,
            'student_id' => $lucas->id,
            'started_at' => now()->subWeeks(2),
            'created_by' => $admin->id,
        ]);

        // Convites em vários estados
        StudentInvite::factory()->used()->create([
            'professional_profile_id' => $personal->id,
            'student_id' => $lucas->id,
            'created_by' => $admin->id,
        ]);

        StudentInvite::factory()->create(['professional_profile_id' => $personal->id, 'created_by' => $admin->id]);
        StudentInvite::factory()->openLink(20)->create(['professional_profile_id' => $nutri->id, 'created_by' => $admin->id]);
        StudentInvite::factory()->expired()->create(['professional_profile_id' => $personal->id, 'created_by' => $admin->id]);
        StudentInvite::factory()->revoked()->create(['professional_profile_id' => $nutri->id, 'created_by' => $admin->id]);

        // 11. Alunos extras com vínculos em vários estados
        Student::factory(10)
            ->create(['created_by' => $admin->id])
            ->each(function (Student $student, int $i) use ($personal, $nutri, $admin) {
                $professional = $i % 2 === 0 ? $personal : $nutri;

                $link = ProfessionalStudent::factory()
                    ->when($i % 5 === 3, fn ($factory) => $factory->pending())
                    ->when($i % 5 === 4, fn ($factory) => $factory->ended())
                    ->create([
                        'professional_profile_id' => $professional->id,
                        'student_id' => $student->id,
                        'service_type' => $professional->id === $personal->id ? 'training' : 'nutrition',
                        'created_by' => $admin->id,
                    ]);

                if ($link->status === 'active' && $i % 2 === 0) {
                    Rating::factory()->create([
                        'professional_student_id' => $link->id,
                        'score' => fake()->numberBetween(3, 5),
                        'created_by' => $admin->id,
                    ]);
                }
            });

        Student::factory()->blocked()->create(['created_by' => $admin->id, 'blocked_by' => $admin->id]);
        Student::factory()->inactive()->create(['created_by' => $admin->id]);

        // 12. Avaliações (publicadas, reportada e oculta para testar moderação)
        Rating::factory()->create([
            'professional_student_id' => $linkPersonal->id,
            'score' => 5,
            'comment' => 'Treinos muito bem montados, evolução visível em um mês.',
            'created_by' => $admin->id,
        ]);

        Rating::factory()->create([
            'professional_student_id' => $linkNutri->id,
            'score' => 5,
            'comment' => 'Cardápio prático e fácil de seguir.',
            'created_by' => $admin->id,
        ]);

        Rating::factory()->reported()->create([
            'professional_student_id' => $linkPersonal->id,
            'score' => 1,
            'created_by' => $admin->id,
        ]);

        Rating::factory()->hidden()->create([
            'professional_student_id' => $linkNutri->id,
            'score' => 2,
            'moderated_by' => $admin->id,
            'created_by' => $admin->id,
        ]);

        // Mantém a média e a contagem dos profissionais coerentes com as avaliações publicadas
        ProfessionalProfile::all()->each(function (ProfessionalProfile $professional) {
            $published = Rating::query()
                ->where('reviewed_type', 'professional')
                ->where('reviewed_id', $professional->id)
                ->where('moderation_status', 'published');

            $professional->forceFill([
                'average_rating' => round((float) $published->avg('score'), 2),
                'ratings_count' => $published->count(),
            ])->save();
        });

        // 13. Chamados de suporte
        SupportTicket::factory()->create([
            'requester_id' => $lucas->id,
            'subject' => 'Não consigo atualizar minha foto de perfil',
            'created_by' => $admin->id,
        ]);

        SupportTicket::factory()->inProgress()->create([
            'requester_type' => 'professional',
            'requester_id' => $pending->id,
            'category' => 'verification_issue',
            'subject' => 'Documento do conselho recusado',
            'assigned_to' => $support->id,
            'created_by' => $admin->id,
        ]);

        SupportTicket::factory()->urgent()->create([
            'requester_id' => $lucas->id,
            'related_professional_id' => $personal->id,
            'category' => 'abuse_report',
            'priority' => 'urgent',
            'subject' => 'Denúncia de conduta inadequada',
            'created_by' => $admin->id,
        ]);

        SupportTicket::factory()->resolved()->create([
            'requester_type' => 'professional',
            'requester_id' => $nutri->id,
            'category' => 'financial_dispute',
            'assigned_to' => $finance->id,
            'resolved_by' => $finance->id,
            'created_by' => $admin->id,
        ]);

        // 14. Exportações de relatórios
        ReportExport::factory()->create(['user_id' => $admin->id]);
        ReportExport::factory()->downloaded()->create(['user_id' => $finance->id, 'report_type' => 'payouts']);
        ReportExport::factory()->processing()->create(['user_id' => $admin->id]);
        ReportExport::factory()->failed()->create(['user_id' => $admin->id]);

        // 15. Logs de auditoria e de acesso à API
        AuditLog::factory(15)->create(['actor_id' => $admin->id, 'actor_name' => $admin->name]);
        AuditLog::factory(3)->warning()->create(['actor_id' => $support->id, 'actor_name' => $support->name]);
        AuditLog::factory(2)->critical()->create(['actor_id' => $admin->id, 'actor_name' => $admin->name]);

        ApiAccessLog::factory(40)->create();
        ApiAccessLog::factory(15)->create([
            'portal' => 'professional_panel',
            'actor_type' => 'professional',
            'actor_id' => $personal->id,
        ]);
    }

    /**
     * Cria especialidades, contato e dados de repasse de um profissional.
     * Sem $slugs, sorteia 2 especialidades compatíveis com o tipo de profissão.
     */
    private function fillProfessional(
        ProfessionalProfile $professional,
        Collection $specialties,
        User $admin,
        ?array $slugs = null,
        array $contact = [],
        array $payout = [],
    ): void {
        $chosen = $slugs
            ? $specialties->whereIn('slug', $slugs)
            : $specialties->whereIn('profession_type', [$professional->profession_type, 'both'])->random(2);

        foreach ($chosen as $specialty) {
            ProfessionalSpecialty::factory()->create([
                'professional_profile_id' => $professional->id,
                'specialty_id' => $specialty->id,
                'created_by' => $admin->id,
            ]);
        }

        ProfessionalContact::factory()->create([
            'professional_profile_id' => $professional->id,
            'created_by' => $admin->id,
            ...$contact,
        ]);

        ProfessionalPayoutSettings::factory()->create([
            'professional_profile_id' => $professional->id,
            'pix_key' => $professional->email,
            'pix_holder_name' => $professional->name,
            'pix_holder_document' => $professional->cpf_cnpj,
            'created_by' => $admin->id,
            ...$payout,
        ]);
    }
}