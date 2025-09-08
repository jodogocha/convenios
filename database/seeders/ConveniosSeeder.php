<?php
// database/seeders/ConveniosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Convenio;
use App\Models\Usuario;
use Carbon\Carbon;

class ConveniosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios para asignar como creadores y coordinadores
        $usuarios = Usuario::all();
        
        if ($usuarios->isEmpty()) {
            $this->command->warn('No hay usuarios en la base de datos. Ejecute primero UsuariosSeeder.');
            return;
        }

        $convenios = [
            [
                'institucion_contraparte' => 'Universidad de São Paulo (USP)',
                'tipo_convenio' => 'Marco',
                'objeto' => 'Establecer un marco de cooperación académica e investigativa entre ambas instituciones para el desarrollo de programas conjuntos de intercambio estudiantil, investigación científica y transferencia de conocimiento en las áreas de ingeniería, arquitectura y ciencias aplicadas.',
                'fecha_firma' => Carbon::now()->subMonths(8),
                'fecha_vencimiento' => Carbon::now()->addYears(3),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado de Investigación',
                'pais_region' => 'Brasil',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Prof. Ana Silva - Rectora USP',
                    'Dra. María González - Vicerrectora de Investigación UNI'
                ],
                'dictamen_numero' => 'DICT-2024-001',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio marco estratégico con amplio potencial de desarrollo.',
                'metadata' => [
                    'area_prioritaria' => 'Ingeniería y Tecnología',
                    'nivel_prioridad' => 'Alto',
                    'contacto_principal' => 'coord.internacional@usp.br'
                ]
            ],
            [
                'institucion_contraparte' => 'Siemens Paraguay S.A.',
                'tipo_convenio' => 'Específico',
                'objeto' => 'Convenio específico para la implementación de un laboratorio de automatización industrial en la Facultad de Ingeniería, incluyendo capacitación de docentes, actualización curricular y desarrollo de proyectos de investigación aplicada en industria 4.0.',
                'fecha_firma' => Carbon::now()->subMonths(6),
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Facultad de Ingeniería',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Ing. Roberto Klaus - Director General Siemens Paraguay',
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería'
                ],
                'dictamen_numero' => 'DICT-2024-015',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Incluye donación de equipos por valor de USD 150,000.',
                'metadata' => [
                    'valor_estimado' => 150000,
                    'moneda' => 'USD',
                    'incluye_equipos' => true,
                    'contacto_principal' => 'r.klaus@siemens.com'
                ]
            ],
            [
                'institucion_contraparte' => 'Universidad Politécnica de Madrid (UPM)',
                'tipo_convenio' => 'Intercambio',
                'objeto' => 'Convenio de intercambio estudiantil y docente para facilitar la movilidad académica entre ambas instituciones, con énfasis en programas de ingeniería civil, arquitectura y ciencias de la computación. Incluye reconocimiento mutuo de créditos académicos.',
                'fecha_firma' => Carbon::now()->subMonths(4),
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Dirección de Relaciones Internacionales',
                'pais_region' => 'España',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Prof. Dr. Guillermo Cisneros - Rector UPM',
                    'Dra. Patricia Ramírez - Directora de Relaciones Internacionales UNI'
                ],
                'dictamen_numero' => 'DICT-2024-023',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio con excelentes resultados en intercambios anteriores.',
                'metadata' => [
                    'estudiantes_por_semestre' => 10,
                    'docentes_por_año' => 4,
                    'areas_intercambio' => ['Ingeniería Civil', 'Arquitectura', 'Informática']
                ]
            ],
            [
                'institucion_contraparte' => 'Ministerio de Obras Públicas y Comunicaciones (MOPC)',
                'tipo_convenio' => 'Cooperación',
                'objeto' => 'Convenio de cooperación técnica para el desarrollo de estudios e investigaciones en infraestructura vial y obras públicas, incluyendo capacitación de funcionarios públicos y desarrollo de proyectos de investigación aplicada.',
                'fecha_firma' => Carbon::now()->subMonths(12),
                'fecha_vencimiento' => Carbon::now()->addYears(4),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Facultad de Ingeniería',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Ing. Ramón González - Ministro MOPC',
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería'
                ],
                'dictamen_numero' => 'DICT-2023-089',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio con impacto directo en el desarrollo nacional.',
                'metadata' => [
                    'funcionarios_capacitados' => 45,
                    'proyectos_desarrollados' => 8,
                    'impacto' => 'Nacional'
                ]
            ],
            [
                'institucion_contraparte' => 'Instituto Tecnológico de Massachusetts (MIT)',
                'tipo_convenio' => 'Investigación',
                'objeto' => 'Convenio para el desarrollo conjunto de investigación en inteligencia artificial aplicada a sistemas de energía renovable, incluyendo intercambio de investigadores, publicaciones conjuntas y desarrollo de patentes.',
                'fecha_firma' => Carbon::now()->subMonths(2),
                'fecha_vencimiento' => Carbon::now()->addYears(3),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado de Investigación',
                'pais_region' => 'Estados Unidos',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Prof. Dr. L. Rafael Reif - Presidente MIT',
                    'Dra. María González - Vicerrectora de Investigación UNI'
                ],
                'dictamen_numero' => 'DICT-2024-045',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio de alto impacto científico y tecnológico.',
                'metadata' => [
                    'presupuesto_investigacion' => 500000,
                    'moneda' => 'USD',
                    'publicaciones_esperadas' => 12,
                    'patentes_esperadas' => 3
                ]
            ],
            [
                'institucion_contraparte' => 'Banco Interamericano de Desarrollo (BID)',
                'tipo_convenio' => 'Específico',
                'objeto' => 'Convenio específico para la implementación del programa "Innovación y Emprendimiento Tecnológico" dirigido a estudiantes y egresados de la UNI, con el objetivo de fomentar el desarrollo de startups tecnológicas.',
                'fecha_firma' => Carbon::now()->subMonths(3),
                'fecha_vencimiento' => Carbon::now()->addMonths(18),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Dirección de Extensión',
                'pais_region' => 'Regional - América Latina',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Lic. Mauricio Claver-Carone - Presidente BID',
                    'Ing. Sandra Torres - Directora de Extensión UNI'
                ],
                'dictamen_numero' => 'DICT-2024-032',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Incluye financiamiento para incubadora de empresas.',
                'metadata' => [
                    'financiamiento' => 250000,
                    'moneda' => 'USD',
                    'startups_objetivo' => 20,
                    'empleos_esperados' => 100
                ]
            ],
            [
                'institucion_contraparte' => 'Universidad Nacional de Asunción (UNA)',
                'tipo_convenio' => 'Marco',
                'objeto' => 'Convenio marco de cooperación interinstitucional para el desarrollo de programas académicos conjuntos, investigación colaborativa e intercambio de recursos humanos y materiales entre ambas universidades públicas.',
                'fecha_firma' => Carbon::now()->subMonths(18),
                'fecha_vencimiento' => null,
                'vigencia_indefinida' => true,
                'coordinador_convenio' => 'Rectorado',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Prof. Dr. Froilán Peralta - Rector UNA',
                    'Dra. María González - Vicerrectora de Investigación UNI'
                ],
                'dictamen_numero' => 'DICT-2023-001',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio estratégico entre universidades públicas nacionales.',
                'metadata' => [
                    'tipo_institucion' => 'Pública',
                    'nivel_cooperacion' => 'Estratégico',
                    'areas_cooperacion' => ['Académica', 'Investigación', 'Extensión']
                ]
            ],
            [
                'institucion_contraparte' => 'Google Paraguay',
                'tipo_convenio' => 'Específico',
                'objeto' => 'Convenio para la implementación del programa Google for Education en la UNI, incluyendo capacitación docente, licencias de software educativo y desarrollo de competencias digitales en estudiantes y profesores.',
                'fecha_firma' => Carbon::now()->subMonths(1),
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado Académico',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Lic. María Fernanda López - Directora Google Paraguay',
                    'Dr. Roberto Silva - Vicerrector Académico UNI'
                ],
                'dictamen_numero' => 'DICT-2024-078',
                'version_final_firmada' => true,
                'estado' => 'pendiente_aprobacion',
                'observaciones' => 'Pendiente activación de cuentas institucionales.',
                'metadata' => [
                    'licencias_incluidas' => 2000,
                    'docentes_capacitar' => 150,
                    'plataformas' => ['Google Classroom', 'Google Meet', 'Google Drive']
                ]
            ],
            [
                'institucion_contraparte' => 'Organización de Estados Iberoamericanos (OEI)',
                'tipo_convenio' => 'Cooperación',
                'objeto' => 'Convenio de cooperación para el fortalecimiento de la educación superior en ciencias y tecnología, incluyendo becas de posgrado, movilidad académica y desarrollo de proyectos de investigación conjuntos.',
                'fecha_firma' => Carbon::now()->subDays(15),
                'fecha_vencimiento' => Carbon::now()->addYears(4),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado de Investigación',
                'pais_region' => 'Iberoamérica',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Dr. Mariano Jabonero - Secretario General OEI'
                ],
                'dictamen_numero' => null,
                'version_final_firmada' => false,
                'estado' => 'borrador',
                'observaciones' => 'En proceso de revisión legal. Falta dictamen jurídico.',
                'metadata' => [
                    'becas_anuales' => 15,
                    'areas_prioritarias' => ['Ingeniería', 'Ciencias Aplicadas', 'Tecnología'],
                    'paises_participantes' => 22
                ]
            ],
            [
                'institucion_contraparte' => 'Telefónica Paraguay',
                'tipo_convenio' => 'Prácticas',
                'objeto' => 'Convenio para la realización de prácticas profesionales de estudiantes de Ingeniería en Telecomunicaciones e Informática en las instalaciones y proyectos de Telefónica Paraguay, con mentoría especializada.',
                'fecha_firma' => Carbon::now()->subMonths(9),
                'fecha_vencimiento' => Carbon::now()->addDays(30), // Próximo a vencer
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Facultad de Tecnología',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Ing. Lorenzo García - Director General Telefónica Paraguay',
                    'Dra. Carmen Villalba - Decana Facultad de Tecnología'
                ],
                'dictamen_numero' => 'DICT-2023-156',
                'version_final_firmada' => true,
                'estado' => 'activo',
                'observaciones' => 'Convenio próximo a vencer. Evaluar renovación.',
                'metadata' => [
                    'practicantes_por_semestre' => 25,
                    'areas_practica' => ['Redes', 'Telecomunicaciones', 'Desarrollo de Software'],
                    'horas_practica' => 400
                ]
            ],
            [
                'institucion_contraparte' => 'Itaipu Binacional',
                'tipo_convenio' => 'Investigación',
                'objeto' => 'Convenio para el desarrollo de investigaciones en energías renovables y sostenibilidad ambiental, con acceso a laboratorios especializados y financiamiento para proyectos de tesis de grado y posgrado.',
                'fecha_firma' => Carbon::now()->subMonths(15),
                'fecha_vencimiento' => Carbon::now()->subDays(60), // Vencido
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado de Investigación',
                'pais_region' => 'Paraguay - Brasil',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI',
                    'Gen. Joaquim Silva e Luna - Director General Brasileño',
                    'Ing. Ernesto Villarejo - Director General Paraguayo'
                ],
                'dictamen_numero' => 'DICT-2022-045',
                'version_final_firmada' => true,
                'estado' => 'vencido',
                'observaciones' => 'Convenio vencido. Pendiente renovación por buenos resultados obtenidos.',
                'metadata' => [
                    'proyectos_desarrollados' => 12,
                    'estudiantes_beneficiados' => 85,
                    'publicaciones_generadas' => 18,
                    'inversion_total' => 300000,
                    'moneda' => 'USD'
                ]
            ],
            [
                'institucion_contraparte' => 'Universidad Católica "Nuestra Señora de la Asunción"',
                'tipo_convenio' => 'Marco',
                'objeto' => 'Marco de cooperación académica para el desarrollo de programas conjuntos de posgrado, investigación colaborativa y uso compartido de recursos bibliotecarios y laboratorios especializados.',
                'fecha_firma' => Carbon::now()->subDays(5),
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'vigencia_indefinida' => false,
                'coordinador_convenio' => 'Vicerrectorado Académico',
                'pais_region' => 'Paraguay',
                'signatarios' => [
                    'Dr. Carlos Mendoza - Rector UNI'
                ],
                'dictamen_numero' => null,
                'version_final_firmada' => false,
                'estado' => 'pendiente_aprobacion',
                'observaciones' => 'Convenio listo para firma. Pendiente coordinación de agenda con Rector UC.',
                'metadata' => [
                    'programas_conjuntos' => 3,
                    'nivel_programas' => 'Posgrado',
                    'recursos_compartidos' => ['Biblioteca', 'Laboratorios', 'Aulas especializadas']
                ]
            ]
        ];

        // Crear los convenios
        foreach ($convenios as $convenioData) {
            // Asignar usuario creador aleatorio
            $convenioData['usuario_creador_id'] = $usuarios->random()->id;
            
            // Asignar usuario coordinador aleatorio (opcional)
            if (rand(0, 1)) {
                $convenioData['usuario_coordinador_id'] = $usuarios->random()->id;
            }
            
            // Si está aprobado, asignar aprobador
            if (in_array($convenioData['estado'], ['aprobado', 'activo'])) {
                $convenioData['usuario_aprobador_id'] = $usuarios->random()->id;
                $convenioData['fecha_aprobacion'] = $convenioData['fecha_firma']->copy()->addDays(rand(1, 15));
            }

            Convenio::create($convenioData);
        }

        $this->command->info('Se han creado ' . count($convenios) . ' convenios de demostración.');
        $this->command->info('Estados creados:');
        $this->command->info('- Activos: ' . collect($convenios)->where('estado', 'activo')->count());
        $this->command->info('- Pendientes: ' . collect($convenios)->where('estado', 'pendiente_aprobacion')->count());
        $this->command->info('- Borradores: ' . collect($convenios)->where('estado', 'borrador')->count());
        $this->command->info('- Vencidos: ' . collect($convenios)->where('estado', 'vencido')->count());
    }
}