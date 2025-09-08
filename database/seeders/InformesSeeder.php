<?php
// database/seeders/InformesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Informe;
use App\Models\Convenio;
use App\Models\Usuario;
use Carbon\Carbon;

class InformesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener convenios activos para crear informes
        $convenios = Convenio::whereIn('estado', ['activo', 'aprobado'])->get();
        $usuarios = Usuario::all();
        
        if ($convenios->isEmpty()) {
            $this->command->warn('No hay convenios disponibles. Ejecute primero ConveniosSeeder.');
            return;
        }

        if ($usuarios->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecute primero UsuariosSeeder.');
            return;
        }

        // Seleccionar algunos convenios para crear informes
        $conveniosParaInformes = $convenios->take(6);

        $informesData = [
            [
                'institucion_co_celebrante' => 'Universidad de São Paulo (USP)',
                'unidad_academica' => 'Vicerrectorado de Investigación',
                'carrera' => 'Múltiples carreras',
                'fecha_celebracion' => Carbon::now()->subMonths(8),
                'vigencia' => '5 años',
                'periodo_evaluado' => 'Enero 2024 - Junio 2024',
                'periodo_desde' => Carbon::create(2024, 1, 1),
                'periodo_hasta' => Carbon::create(2024, 6, 30),
                'dependencia_responsable' => 'Dirección de Cooperación Internacional',
                'coordinadores_designados' => [
                    'Dra. María González - Vicerrectora de Investigación',
                    'Dr. José Martínez - Director de Cooperación Internacional',
                    'Prof. Ana López - Coordinadora de Intercambios'
                ],
                'convenio_celebrado_propuesta' => 'Aprobado por Consejo Directivo mediante Resolución CD-2024-045',
                'tipo_convenio' => 'Marco',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 12,
                'logros_obtenidos' => 'Se logró establecer un programa de intercambio estudiantil exitoso con 8 estudiantes de UNI realizando semestre académico en USP y 6 estudiantes brasileños en UNI. Se iniciaron 3 proyectos de investigación conjuntos en las áreas de inteligencia artificial, biotecnología e ingeniería ambiental.',
                'beneficios_alcanzados' => 'Mejora significativa en la formación internacional de estudiantes, establecimiento de redes de investigación sólidas, incremento en publicaciones científicas conjuntas (5 artículos publicados), y fortalecimiento de capacidades docentes mediante intercambio de profesores.',
                'dificultades_incidentes' => 'Inicialmente se presentaron demoras en los procesos de visado para estudiantes paraguayos. Se solucionó mediante coordinación directa con el Consulado de Brasil en Paraguay.',
                'responsabilidad_instalaciones' => 'UNI: Aulas especializadas y laboratorios de investigación. USP: Laboratorios avanzados de biotecnología y biblioteca especializada.',
                'sugerencias_mejoras' => 'Implementar un sistema de seguimiento digital para estudiantes en intercambio. Establecer convenios específicos con aerolíneas para descuentos en pasajes. Crear un fondo de emergencia para gastos imprevistos.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/1AbC2dEfGhI3jKlM4nOpQ5rStU6vWxY7z',
                'informacion_complementaria' => 'El convenio ha superado las expectativas iniciales. Se recomienda ampliarlo a otras áreas como arquitectura y ciencias químicas.',
                'anexos' => [
                    'Fotos de actividades de intercambio',
                    'Certificados de participación en conferencias',
                    'Artículos científicos publicados',
                    'Videos de laboratorios conjuntos'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/1AbC2dEfGhI3jKlM4nOpQ5rStU6vWxY7z',
                'firmas' => [
                    'Dra. María González - Vicerrectora de Investigación',
                    'Dr. José Martínez - Director de Cooperación Internacional'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(30),
                'estado' => 'aprobado'
            ],
            [
                'institucion_co_celebrante' => 'Siemens Paraguay S.A.',
                'unidad_academica' => 'Facultad de Ingeniería',
                'carrera' => 'Ingeniería Industrial, Ingeniería Electrónica',
                'fecha_celebracion' => Carbon::now()->subMonths(6),
                'vigencia' => '2 años',
                'periodo_evaluado' => 'Marzo 2024 - Agosto 2024',
                'periodo_desde' => Carbon::create(2024, 3, 1),
                'periodo_hasta' => Carbon::create(2024, 8, 31),
                'dependencia_responsable' => 'Decanato de la Facultad de Ingeniería',
                'coordinadores_designados' => [
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería',
                    'Ing. Carlos Benítez - Director de Laboratorios',
                    'Dra. Sandra Morales - Coordinadora Académica'
                ],
                'convenio_celebrado_propuesta' => 'Propuesta aprobada por Consejo de Facultad RCF-2024-012',
                'tipo_convenio' => 'Específico',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 18,
                'logros_obtenidos' => 'Implementación exitosa del Laboratorio de Automatización Industrial con equipos de última tecnología. Capacitación de 25 docentes en tecnologías de Industria 4.0. Desarrollo de 6 proyectos de tesis de grado relacionados con automatización.',
                'beneficios_alcanzados' => 'Modernización significativa de la infraestructura académica. Mejora en la employabilidad de egresados (100% de inserción laboral en el área). Establecimiento de UNI como referente regional en automatización industrial. Ahorro estimado de USD 50,000 en equipamiento.',
                'dificultades_incidentes' => 'Retraso inicial en la entrega de equipos por problemas de importación. Necesidad de adecuación eléctrica del laboratorio que no estaba contemplada inicialmente.',
                'responsabilidad_instalaciones' => 'UNI: Adecuación eléctrica, mantenimiento preventivo, seguridad del laboratorio. Siemens: Mantenimiento correctivo de equipos, actualización de software, capacitación técnica.',
                'sugerencias_mejoras' => 'Incluir en futuros convenios la adecuación de infraestructura básica. Establecer un cronograma más flexible para entregas. Crear un protocolo de mantenimiento conjunto.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/2BcD3eFgHiJ4kLm5NoP6qRsT7uVwX8yZ9',
                'informacion_complementaria' => 'El impacto ha sido extraordinario. Se propone extender el convenio a otras áreas de la ingeniería.',
                'anexos' => [
                    'Videos del laboratorio en funcionamiento',
                    'Certificados de capacitación docente',
                    'Proyectos de tesis desarrollados',
                    'Estadísticas de uso del laboratorio'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/2BcD3eFgHiJ4kLm5NoP6qRsT7uVwX8yZ9',
                'firmas' => [
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería',
                    'Ing. Carlos Benítez - Director de Laboratorios'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(45),
                'estado' => 'aprobado'
            ],
            [
                'institucion_co_celebrante' => 'Universidad Politécnica de Madrid (UPM)',
                'unidad_academica' => 'Dirección de Relaciones Internacionales',
                'carrera' => 'Ingeniería Civil, Arquitectura, Informática',
                'fecha_celebracion' => Carbon::now()->subMonths(4),
                'vigencia' => '5 años',
                'periodo_evaluado' => 'Abril 2024 - Septiembre 2024',
                'periodo_desde' => Carbon::create(2024, 4, 1),
                'periodo_hasta' => Carbon::create(2024, 9, 30),
                'dependencia_responsable' => 'Dirección de Relaciones Internacionales',
                'coordinadores_designados' => [
                    'Dra. Patricia Ramírez - Directora de Relaciones Internacionales',
                    'Arq. Miguel Rodríguez - Coordinador de Movilidad',
                    'Ing. Laura Sánchez - Coordinadora Académica'
                ],
                'convenio_celebrado_propuesta' => 'Aprobado mediante Resolución Rectoral RR-2024-089',
                'tipo_convenio' => 'Marco',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 15,
                'logros_obtenidos' => 'Exitoso programa de intercambio con 12 estudiantes UNI en Madrid y 8 estudiantes españoles en Paraguay. Participación en 3 conferencias internacionales conjuntas. Desarrollo de 2 proyectos de investigación colaborativa en arquitectura sostenible.',
                'beneficios_alcanzados' => 'Excelente formación internacional de estudiantes con promedio académico superior a 4.2/5.0. Establecimiento de vínculos académicos duraderos. Mejora en competencias lingüísticas (100% de estudiantes alcanzó nivel B2 en español/inglés). Incremento en la internacionalización de UNI.',
                'dificultades_incidentes' => 'Diferencias en sistemas de calificación que requirieron tabla de equivalencias detallada. Algunos estudiantes necesitaron apoyo adicional en adaptación cultural.',
                'responsabilidad_instalaciones' => 'UNI: Alojamiento temporal para estudiantes españoles, acceso a biblioteca y laboratorios. UPM: Residencias estudiantiles, laboratorios especializados, biblioteca digital.',
                'sugerencias_mejoras' => 'Implementar programa de orientación cultural más extenso. Establecer sistema de buddy/mentor estudiantil. Crear fondo de ayuda económica para estudiantes con dificultades.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/3CdE4fGhIjK5lM6nOpQ7rStU8vWxY9zA1',
                'informacion_complementaria' => 'Excelente evaluación por parte de todos los participantes. Se recomienda incrementar el número de plazas de intercambio.',
                'anexos' => [
                    'Reportes académicos de estudiantes',
                    'Fotografías de actividades culturales',
                    'Certificados de participación en conferencias',
                    'Encuestas de satisfacción'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/3CdE4fGhIjK5lM6nOpQ7rStU8vWxY9zA1',
                'firmas' => [
                    'Dra. Patricia Ramírez - Directora de Relaciones Internacionales',
                    'Arq. Miguel Rodríguez - Coordinador de Movilidad'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(20),
                'estado' => 'enviado'
            ],
            [
                'institucion_co_celebrante' => 'Ministerio de Obras Públicas y Comunicaciones (MOPC)',
                'unidad_academica' => 'Facultad de Ingeniería',
                'carrera' => 'Ingeniería Civil',
                'fecha_celebracion' => Carbon::now()->subMonths(12),
                'vigencia' => '4 años',
                'periodo_evaluado' => 'Enero 2024 - Diciembre 2024',
                'periodo_desde' => Carbon::create(2024, 1, 1),
                'periodo_hasta' => Carbon::create(2024, 12, 31),
                'dependencia_responsable' => 'Departamento de Ingeniería Civil',
                'coordinadores_designados' => [
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería',
                    'Ing. Roberto Mendoza - Jefe Dpto. Ing. Civil',
                    'Dra. Carmen Villalba - Coordinadora de Investigación'
                ],
                'convenio_celebrado_propuesta' => 'Convenio estratégico aprobado por Consejo Universitario CU-2023-156',
                'tipo_convenio' => 'Específico',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 22,
                'logros_obtenidos' => 'Capacitación exitosa de 45 funcionarios del MOPC en nuevas tecnologías de construcción. Desarrollo de 8 estudios técnicos sobre infraestructura vial nacional. Creación de 3 manuales técnicos para obras públicas. Establecimiento de laboratorio de materiales certificado.',
                'beneficios_alcanzados' => 'Fortalecimiento significativo de capacidades técnicas del sector público. Mejora en la calidad de obras de infraestructura nacional. Generación de conocimiento aplicado con impacto directo en el desarrollo del país. Vinculación efectiva universidad-Estado.',
                'dificultades_incidentes' => 'Coordinar horarios de capacitación con las actividades laborales de funcionarios públicos. Limitaciones presupuestarias iniciales para algunos estudios técnicos.',
                'responsabilidad_instalaciones' => 'UNI: Aulas de capacitación, laboratorios de ensayo de materiales, biblioteca técnica. MOPC: Acceso a obras en ejecución, equipos especializados de campo, transporte para visitas técnicas.',
                'sugerencias_mejoras' => 'Establecer modalidades de capacitación virtual para mayor flexibilidad. Incrementar el presupuesto para estudios de mayor envergadura. Crear programa de becas para funcionarios en posgrados.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/4DeF5gHiJkL6mN7oPqR8sTuV9wXyZ0aB2',
                'informacion_complementaria' => 'El convenio ha demostrado ser altamente beneficioso para el país. Se recomienda su renovación y ampliación a otras áreas técnicas.',
                'anexos' => [
                    'Manuales técnicos desarrollados',
                    'Informes de estudios realizados',
                    'Certificados de capacitación',
                    'Documentación fotográfica de obras visitadas'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/4DeF5gHiJkL6mN7oPqR8sTuV9wXyZ0aB2',
                'firmas' => [
                    'Dr. Luis Fernández - Decano Facultad de Ingeniería',
                    'Ing. Roberto Mendoza - Jefe Dpto. Ing. Civil',
                    'Dra. Carmen Villalba - Coordinadora de Investigación'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(60),
                'estado' => 'aprobado'
            ],
            [
                'institucion_co_celebrante' => 'Telefónica Paraguay',
                'unidad_academica' => 'Facultad de Tecnología',
                'carrera' => 'Ing. en Telecomunicaciones, Ing. Informática',
                'fecha_celebracion' => Carbon::now()->subMonths(9),
                'vigencia' => '2 años (próximo a vencer)',
                'periodo_evaluado' => 'Febrero 2024 - Julio 2024',
                'periodo_desde' => Carbon::create(2024, 2, 1),
                'periodo_hasta' => Carbon::create(2024, 7, 31),
                'dependencia_responsable' => 'Decanato Facultad de Tecnología',
                'coordinadores_designados' => [
                    'Dra. Carmen Villalba - Decana Facultad de Tecnología',
                    'Ing. Pedro Gómez - Director de Prácticas Profesionales',
                    'Prof. Ana María Torres - Coordinadora de Vinculación'
                ],
                'convenio_celebrado_propuesta' => 'Aprobado por Consejo de Facultad de Tecnología CFT-2023-089',
                'tipo_convenio' => 'Específico',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 28,
                'logros_obtenidos' => 'Exitosa inserción de 50 estudiantes en prácticas profesionales con excelente desempeño (promedio 4.5/5.0). Desarrollo de 12 proyectos innovadores en telecomunicaciones. 80% de practicantes recibió ofertas laborales. Actualización curricular basada en necesidades del sector.',
                'beneficios_alcanzados' => 'Formación práctica de alta calidad para estudiantes. Fortalecimiento de vínculos con el sector privado. Mejora en la empleabilidad de egresados. Retroalimentación valiosa para actualización de planes de estudio. Identificación de talentos por parte de la empresa.',
                'dificultades_incidentes' => 'Algunos estudiantes requirieron capacitación adicional en herramientas específicas de la empresa. Coordinación de horarios académicos con actividades empresariales.',
                'responsabilidad_instalaciones' => 'UNI: Preparación académica previa, seguimiento tutorial, evaluación de competencias. Telefónica: Espacios de trabajo, equipos tecnológicos, mentoría especializada, certificación de competencias.',
                'sugerencias_mejoras' => 'Incluir módulo de inducción empresarial en el currículo. Establecer programa de actualización docente en tecnologías empresariales. Ampliar convenio a modalidades de trabajo de grado.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/5EfG6hIjKlM7nO8pQrS9tUvW0xYzA1bC3',
                'informacion_complementaria' => 'Convenio modelo para replicar con otras empresas del sector. Se recomienda enfáticamente su renovación y ampliación.',
                'anexos' => [
                    'Evaluaciones de desempeño de practicantes',
                    'Proyectos desarrollados por estudiantes',
                    'Ofertas laborales recibidas',
                    'Videos testimoniales de estudiantes'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/5EfG6hIjKlM7nO8pQrS9tUvW0xYzA1bC3',
                'firmas' => [
                    'Dra. Carmen Villalba - Decana Facultad de Tecnología',
                    'Ing. Pedro Gómez - Director de Prácticas Profesionales'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(15),
                'estado' => 'borrador'
            ],
            [
                'institucion_co_celebrante' => 'Instituto Tecnológico de Massachusetts (MIT)',
                'unidad_academica' => 'Vicerrectorado de Investigación',
                'carrera' => 'Posgrado - Múltiples áreas',
                'fecha_celebracion' => Carbon::now()->subMonths(2),
                'vigencia' => '3 años',
                'periodo_evaluado' => 'Agosto 2024 - Octubre 2024',
                'periodo_desde' => Carbon::create(2024, 8, 1),
                'periodo_hasta' => Carbon::create(2024, 10, 31),
                'dependencia_responsable' => 'Instituto de Investigaciones UNI',
                'coordinadores_designados' => [
                    'Dra. María González - Vicerrectora de Investigación',
                    'Dr. Fernando López - Director Instituto de Investigaciones',
                    'PhD. Carolina Mendez - Coordinadora de Proyectos Internacionales'
                ],
                'convenio_celebrado_propuesta' => 'Convenio de alto impacto aprobado por Consejo Superior CS-2024-001',
                'tipo_convenio' => 'Específico',
                'convenio_ejecutado' => true,
                'numero_actividades_realizadas' => 8,
                'logros_obtenidos' => 'Inicio exitoso de proyecto de investigación en IA aplicada a energías renovables. Intercambio de 4 investigadores (2 de cada institución). Participación en conferencia internacional MIT Energy Initiative. Desarrollo de propuesta conjunta para funding internacional.',
                'beneficios_alcanzados' => 'Acceso a tecnología y metodologías de investigación de clase mundial. Posicionamiento de UNI en redes de investigación internacionales de élite. Capacitación avanzada de investigadores locales. Potencial desarrollo de patentes conjuntas.',
                'dificultades_incidentes' => 'Complejidad en procesos de visado para investigadores. Diferencias en procedimientos administrativos que requirieron adaptación.',
                'responsabilidad_instalaciones' => 'UNI: Laboratorio de energías renovables, facilidades de alojamiento para investigadores visitantes. MIT: Laboratorios avanzados de IA, supercomputadoras, biblioteca digital especializada.',
                'sugerencias_mejoras' => 'Establecer oficina de enlace permanente. Crear protocolo específico para trámites de visado de investigadores. Implementar plataforma digital colaborativa.',
                'anexo_evidencias' => 'https://drive.google.com/drive/folders/6FgH7iJkLmN8oP9qRsT0uVwX1yZaB2cD4',
                'informacion_complementaria' => 'Convenio de altísimo valor estratégico. Aunque en etapa inicial, los resultados son muy prometedores.',
                'anexos' => [
                    'Propuesta de investigación aprobada',
                    'Reportes de intercambio de investigadores',
                    'Presentaciones en conferencias',
                    'Documentos de propiedad intelectual'
                ],
                'enlace_google_drive' => 'https://drive.google.com/drive/folders/6FgH7iJkLmN8oP9qRsT0uVwX1yZaB2cD4',
                'firmas' => [
                    'Dra. María González - Vicerrectora de Investigación',
                    'Dr. Fernando López - Director Instituto de Investigaciones'
                ],
                'fecha_presentacion' => Carbon::now()->subDays(5),
                'estado' => 'enviado'
            ]
        ];

        // Crear informes para los convenios seleccionados
        foreach ($conveniosParaInformes as $index => $convenio) {
            if (isset($informesData[$index])) {
                $informeData = $informesData[$index];
                $informeData['convenio_id'] = $convenio->id;
                $informeData['usuario_creador_id'] = $usuarios->random()->id;
                
                // Asignar revisor si el informe está aprobado
                if ($informeData['estado'] === 'aprobado') {
                    $informeData['usuario_revisor_id'] = $usuarios->random()->id;
                    $informeData['fecha_revision'] = $informeData['fecha_presentacion']->copy()->addDays(rand(5, 15));
                }

                Informe::create($informeData);
            }
        }

        $this->command->info('Se han creado ' . count($informesData) . ' informes de demostración.');
        $this->command->info('Estados de informes creados:');
        $this->command->info('- Aprobados: ' . collect($informesData)->where('estado', 'aprobado')->count());
        $this->command->info('- Enviados: ' . collect($informesData)->where('estado', 'enviado')->count());
        $this->command->info('- Borradores: ' . collect($informesData)->where('estado', 'borrador')->count());
    }
}